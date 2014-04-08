<?php

namespace LittleBigJoe\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event;
use Symfony\Component\DependencyInjection\ContainerAware;

class ProjectListener extends ContainerAware
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
			
				// Insertions
				foreach ($uow->getScheduledEntityInsertions() as $project) 
				{
						if ($project instanceof Project)
						{
								// Make sure the wallet is created once in MangoPay
								if ($project->getMangopayWalletId() == null || $project->getMangopayWalletId() == 0)
								{
										// Create project in MangoPay
										$api = $this->container->get('little_big_joe_mango_pay.api');
										$mangopayProject = $api->createProject($project->getUser()->getMangopayUserId(), array($project->getUser()->getMangopayUserId()), $project->getId(), $project->getName(), $project->getPitch(), $project->getAmountRequired(), $project->getEndingAt()->getTimestamp());
										if (!empty($mangopayProject))
										{
												if (!empty($mangopayProject->ID))
												{
														$project->setMangopayWalletId($mangopayProject->ID);
												}
												if (!empty($mangopayProject->CreationDate))
												{
														$project->setMangopayCreatedAt(new \DateTime('@'.$mangopayProject->CreationDate));
														$project->setMangopayUpdatedAt(new \DateTime('@'.$mangopayProject->CreationDate));
												}
												if (!empty($mangopayProject->UpdateDate))
												{
														$project->setMangopayUpdatedAt(new \DateTime('@'.$mangopayProject->UpdateDate));
												}
										}
								}
						}
				}
								
				// Updates
				foreach ($uow->getScheduledEntityUpdates() as $project) 
				{
						if ($project instanceof Project)
						{
								// Make sure we update the wallet if it's already created
								if ($project->getMangopayWalletId() != null && $project->getMangopayWalletId() != 0 )
								{
										// Update project in MangoPay
										$api = $this->container->get('little_big_joe_mango_pay.api');
										$mangopayProject = $api->updateProject($project->getMangopayWalletId(), $project->getId(), $project->getName(), $project->getPitch(), $project->getAmountRequired(), $project->getEndingAt()->getTimestamp());
										if (!empty($mangopayProject))
										{
												if (!empty($mangopayProject->UpdateDate))
												{
														$project->setMangopayUpdatedAt(new \DateTime('@'.$mangopayProject->UpdateDate));
												}
										}	
								}
								
								// Update status if amount required has been reached, and send email
								if ($project->getAmountRequired() == $project->getAmountCount() && $project->getStatus() == '2')
								{
										$project->setStatus('3');
											
										// Send welcome email
										$email = \Swift_Message::newInstance()
															->setContentType('text/html')
															->setSubject($this->container->get('translator')->trans('Your project is fully funded'))
															->setFrom($this->container->getParameter('default_email_address'))
															->setTo(array($project->getUser()->getEmail() => $project->getUser()))
															->setBody(
																	$this->container->get('templating')->render('LittleBigJoeFrontendBundle:Email:update_finished_phase_project.html.twig', array(
																			'user' => $project->getUser(),
																			'project' => $project,
											        				'url' => $this->container->get('request')->getSchemeAndHttpHost()
																	), 'text/html')
															);
										$this->container->get('mailer')->send($email);
								}
						}
				}
		}
}