<?php

namespace LittleBigJoe\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProjectContributionListener
{
		protected $container;
		
		public function __construct(ContainerInterface $container)
		{
				$this->container = $container;
		}
	
		public function postPersist(LifecycleEventArgs $args)
		{
				$projectContribution = $args->getEntity();
				$em = $args->getEntityManager();
			
				if ($projectContribution instanceof ProjectContribution) 
				{	
						$project = $projectContribution->getProject();
						
						if ($project instanceof Project) 
						{
								$project->setAmountCount($project->getAmountCount() + $projectContribution->getMangopayAmount());
								$em->persist($project);
								$em->flush();
								
								// Update status if amount required has been reached, and send email
								if ($project->getAmountRequired() <= $project->getAmountCount() && $project->getEndedAt() == null)
								{
										$project->setEndedAt(new \DateTime());
										$em->persist($project);
										$em->flush();
											
										// Send project finished email
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
}