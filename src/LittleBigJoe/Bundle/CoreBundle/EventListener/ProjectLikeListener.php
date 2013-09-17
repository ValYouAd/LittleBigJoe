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
								
								// Update status if likes required has been reached, and send email
								if ($project->getLikesRequired() == $project->getLikesCount() && $project->getStatus() == '1')
								{
										$project->setStatus('2');
										$em->persist($project);
										$em->flush();
									
										// Send updated project email
										$email = \Swift_Message::newInstance()
										->setSubject($this->container->get('translator')->trans('Your project has been updated'))
										->setFrom($this->container->getParameter('default_email_address'))
										->setTo(array($project->getUser()->getEmail() => $project->getUser()))
										->setBody(
						        		$this->container->get('templating')->render('LittleBigJoeFrontendBundle:Email:update_funding_phase_project.html.twig', array(
						        				'user' => $project->getUser(),
						        				'project' => $project
						        		), 'text/html')
						        );
										$this->container->get('mailer')->send($email);
								}
						}
				}
		}
}