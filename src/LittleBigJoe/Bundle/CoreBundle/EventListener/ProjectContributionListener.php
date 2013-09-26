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
						}
				}
		}
}

/*
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProjectContributionListener
{
		protected $container;
		
		public function __construct(ContainerInterface $container)
		{
				$this->container = $container;
		}
	
		public function postUpdate(LifecycleEventArgs $args)
		{
				$projectContribution = $args->getEntity();
				$em = $args->getEntityManager();
			
				if ($projectContribution instanceof ProjectContribution) 
				{	
					$projectContribution->setMangopayAmount(200000000);
					$em->persist($projectContribution);
					
						$project = $projectContribution->getProject();
						//$reward = $projectContribution->getReward();
						
						if ($project instanceof Project) 
						{
								// Only update amount count, if transaction is OK
								if ($projectContribution->getMangopayIsSucceeded() == true && 
										$projectContribution->getMangopayIsCompleted() == true)
								{
										$projectContribution->setMangopayAmount(100000000);
										$em->persist($projectContribution);
										// Update funding amount
										$project->setAmountCount(100000000);
										$em->persist($project);
										
										// Decrement stock available for associated reward if there's an associated reward
										if ($reward instanceof ProjectReward && $reward->getStock() != null)
										{
												$reward->setStock($reward->getStock() - 1);
												$em->persist($reward);
												$em->flush();		
										}
								}						
						}
				}
		}
}*/