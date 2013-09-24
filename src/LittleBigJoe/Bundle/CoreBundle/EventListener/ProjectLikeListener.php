<?php

namespace LittleBigJoe\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectLike;
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
								$project->setLikesCount($project->getLikesCount() + 1);
								$em->persist($project);
								$em->flush();
						}
				}
		}
}