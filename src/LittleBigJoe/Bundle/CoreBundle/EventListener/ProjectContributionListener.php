<?php

    namespace LittleBigJoe\Bundle\CoreBundle\EventListener;

    use Doctrine\ORM\Event\LifecycleEventArgs;
    use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
    use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution;
    use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward;
    use Symfony\Component\DependencyInjection\ContainerInterface;
    use Doctrine\ORM\Event;
    use Symfony\Component\DependencyInjection\ContainerAware;

    class ProjectContributionListener extends ContainerAware
    {
        protected $container;

        public function __construct(ContainerInterface $container)
        {
            $this->container = $container;
        }

        public function onFlush(Event\OnFlushEventArgs $eventArgs)
        {
            $em = $eventArgs->getEntityManager();
            $uow = $em->getUnitOfWork();

            // Updates
            foreach ($uow->getScheduledEntityUpdates() as $projectContribution)
            {
                if ($projectContribution instanceof ProjectContribution)
                {
                    $project = $projectContribution->getProject();
                    $previousTotalParticipants = $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->countParticipants($project->getId());
                    $reward = $projectContribution->getReward();

                    if ($project instanceof Project)
                    {
                        // Only update amount count, if transaction is OK
                        if ($projectContribution->getMangopayIsSucceeded() == true &&
                            $projectContribution->getMangopayIsCompleted() == true)
                        {
                            // Update funding amount
                            $project->setAmountCount($project->getAmountCount() + $projectContribution->getMangopayAmount());

                            // Decrement stock available for associated reward if there's an associated reward
                            // and if there's a limited stock
                            if ($reward instanceof ProjectReward && $reward->getStock() != null)
                            {
                                $reward->setStock($reward->getStock() - 1);

                                // Save changes
                                $meta = $em->getClassMetadata(get_class($reward));
                                $uow->computeChangeSet($meta, $reward);
                            }

                            // Save changes
                            $meta = $em->getClassMetadata(get_class($project));
                            $uow->computeChangeSet($meta, $project);
                        }
                    }

                    $totalParticipants = $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->countParticipants($project->getId());

                    // Check that the project has at least 10 participants, and has reached a new step
                    if ($totalParticipants >= 10 && (($previousTotalParticipants < 10) || (substr($totalParticipants, 0, 1) > substr($previousTotalParticipants, 0, 1))))
                    {
                        // Get followers
                        $followersIds = array();
                        $brandFollowers = $project->getBrand()->getFollowers();
                        if (!empty($brandFollowers))
                        {
                            foreach ($brandFollowers as $brandFollower)
                            {
                                if (!in_array($brandFollower->getId(), $followersIds))
                                {
                                    $followersIds[] = $brandFollower->getId();
                                }
                            }
                        }
                        $userFollowers = $project->getUser()->getFollowers();
                        if (!empty($userFollowers))
                        {
                            foreach ($userFollowers as $userFollower)
                            {
                                if (!in_array($userFollower->getId(), $followersIds))
                                {
                                    $followersIds[] = $userFollower->getId();
                                }
                            }
                        }

                        // Generate notifications
                        if (!empty($followersIds))
                        {
                            foreach ($followersIds as $followerId)
                            {
                                $follower = $em->getRepository('LittleBigJoeCoreBundle:User')->find($followerId);

                                $notification = new Notification();
                                $notification->setUser($follower);
                                $notification->setProject($project);
                                $notification->setType(1);
                                $notification->setData(array('step' => $totalParticipants));

                                $em->persist($notification);
                                $em->flush();
                            }
                        }
                    }
                }
            }
        }
    }