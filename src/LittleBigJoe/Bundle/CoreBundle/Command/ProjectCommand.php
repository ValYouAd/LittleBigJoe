<?php

namespace LittleBigJoe\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;

class ProjectCommand extends ContainerAwareCommand
{
		/**
		 * Command configuration
		 * @see \Symfony\Component\Console\Command\Command::configure()
		 */
		protected function configure()
		{
				$this
						->setName('lbj:update-project')
						->setDescription('Allows to update project when they have reached their ending date')
				;
		}

		/**
		 * Import tyres
		 * @see \Symfony\Component\Console\Command\Command::configure()
		 */
		protected function execute(InputInterface $input, OutputInterface $output)
		{
				// Used for log display
				$progress = $this->getHelperSet()->get('progress');
				$formatter = $this->getHelperSet()->get('formatter');
				$em = $this->getContainer()->get('doctrine')->getManager();
				$totalUpdatedProjects = 0;
				
				// Get all projects
				$projects = $em->getRepository('LittleBigJoeCoreBundle:Project')->findAll();			
				$foundMessages = array(sizeof($projects).' project(s) found !', sizeof($projects).' project(s) has been found in database', 'Launching parser ...');
				$formattedBlock = $formatter->formatBlock($foundMessages, 'comment', true);
				$output->writeln($formattedBlock);
				$progress->start($output, sizeof($projects));
				
				foreach ($projects as $project)
				{
						// Only update projects that should have been ended
						if ($project->getEndingAt() <= new \DateTime() && $project->getEndedAt() == null)	
						{
								$project->setEndedAt(new \DateTime());
								
								$em->persist($project);
								$em->flush();
								
								$totalUpdatedProjects++;
								
								// Send project finished email
								$email = \Swift_Message::newInstance()
												->setContentType('text/html')
												->setSubject($this->getContainer()->get('translator')->trans('Your project has been updated'))
												->setFrom($this->getContainer()->getParameter('default_email_address'))
												->setTo(array($project->getUser()->getEmail() => $project->getUser()))
												->setBody(
														$this->getContainer()->get('templating')->render('LittleBigJoeFrontendBundle:Email:update_finished_phase_project.html.twig', array(
																'user' => $project->getUser(),
																'project' => $project,
																'url' => $this->getContainer()->getParameter('default_url')
														), 'text/html')
												);
								$this->getContainer()->get('mailer')->send($email);
						}	
						
						$progress->advance();		
				}
				
				// End of import
				$progress->finish();
				$updatedMessages = array($totalUpdatedProjects.' project(s) updated !', $totalUpdatedProjects.' project(s) has been updated, and setted as ended in database');
				$formattedBlock = $formatter->formatBlock($updatedMessages, 'comment', true);
				$output->writeln($formattedBlock);				

				// Make sure emails are sended, even in DEV environment
				$transport = $this->getContainer()->get('mailer')->getTransport();
				if (!$transport instanceof \Swift_Transport_SpoolTransport) 
				{
						return;
				}
				
				$spool = $transport->getSpool();
				if (!$spool instanceof \Swift_MemorySpool) 
				{
						return;
				}
				
				$spool->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
		}
}