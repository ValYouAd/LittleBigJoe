<?php

namespace LittleBigJoe\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
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
						$project = $projectContribution->getProject();
						$reward = $projectContribution->getReward();
						
						if ($project instanceof Project && $reward instanceof ProjectReward) 
						{
								// Only update amount count, if transaction is OK
								if ($projectContribution->getMangopayIsSucceeded() == true && 
										$projectContribution->getMangopayIsCompleted() == true)
								{
										// Update funding amount
										$project->setAmountCount($project->getAmountCount() + $projectContribution->getMangopayAmount());
										$em->persist($project);
										
										// Decrement stock available for associated reward
										$reward->setStock($reward->getStock() - 1);
										$em->persist($reward);
										$em->flush();		
								}						
						}
				}
		}
}