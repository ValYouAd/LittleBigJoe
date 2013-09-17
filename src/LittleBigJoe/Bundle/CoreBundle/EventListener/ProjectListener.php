<?php

namespace LittleBigJoe\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProjectListener
{
		protected $container;
		
		public function __construct(ContainerInterface $container)
		{
				$this->container = $container;
		}
	
		public function postUpdate(LifecycleEventArgs $args)
		{
				$project = $args->getEntity();
				$em = $args->getEntityManager();
			
				if ($project instanceof Project) 
				{
						// Update status if likes required has been reached, and send email
						if ($project->getLikesRequired() == $project->getLikesCount() && $project->getStatus() == '1')
						{
								$project->setStatus('2');
								$em->persist($project);
								$em->flush();
							
								// Send welcome email
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
						
						// Update status if amount required has been reached, and send email
						if ($project->getAmountRequired() == $project->getAmountCount() && $project->getStatus() == '2')
						{
								$project->setStatus('3');
								$em->persist($project);
								$em->flush();
									
								// Send welcome email
								$email = \Swift_Message::newInstance()
								->setSubject($this->container->get('translator')->trans('Your project is fully funded'))
								->setFrom($this->container->getParameter('default_email_address'))
								->setTo(array($project->getUser()->getEmail() => $project->getUser()))
								->setBody(
										$this->container->get('templating')->render('LittleBigJoeFrontendBundle:Email:update_finished_phase_project.html.twig', array(
												'user' => $project->getUser(),
												'project' => $project
										), 'text/html')
								);
								$this->container->get('mailer')->send($email);
						}
				}
		}
}