<?php

namespace LittleBigJoe\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectLike;
use LittleBigJoe\Bundle\CoreBundle\Entity\Notification;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProjectLikeListener
{
		protected $container;
		
		public function __construct(ContainerInterface $container)
		{
				$this->container = $container;
		}
	
		public function postPersist(LifecycleEventArgs $args)
		{				
			$projectLike = $args->getEntity();
			$em = $args->getEntityManager();
										
			if ($projectLike instanceof ProjectLike) 
			{	
				$project = $projectLike->getProject();
																
				if ($project instanceof Project) 
				{			
				    $previousTotalLikes = $project->getLikesCount();
			        $totalLikes = $project->getLikesCount() + 1;
					$project->setLikesCount($totalLikes);
					
					// Check that the project has at least 10 likes, and has reached a new step
					if ($totalLikes >= 10 && (($previousTotalLikes < 10) || (substr($totalLikes, 0, 1) > substr($previousTotalLikes, 0, 1))))
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
					            $notification->setType(0);
					            $notification->setData(array('step' => $totalLikes));
					            
					            $em->persist($notification);
					            $em->flush();
					        }
					    }					    				    	
					}

					$em->persist($project);
					$em->flush();
				}
			}
		}
}