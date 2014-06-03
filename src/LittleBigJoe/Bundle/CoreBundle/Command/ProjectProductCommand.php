<?php

namespace LittleBigJoe\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;

class ProjectProductCommand extends ContainerAwareCommand
{
    /**
     * Command configuration
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('lbj:update-project-product')
            ->setDescription('Allows to update project product');
    }

    /**
     * Update project products
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Used for log display
        $progress = $this->getHelperSet()->get('progress');
        $formatter = $this->getHelperSet()->get('formatter');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $totalUpdatedProjects = 0;

        // Get all project products
        $projectProducts = $em->getRepository('LittleBigJoeCoreBundle:ProjectProduct')->findBy(array('validatedAt' => null));
        $foundMessages = array(sizeof($projectProducts) . ' product(s) found !', sizeof($projectProducts) . ' product(s) has been found in database', 'Launching parser ...');
        $formattedBlock = $formatter->formatBlock($foundMessages, 'comment', true);
        $output->writeln($formattedBlock);
        $progress->start($output, sizeof($projectProducts));

        foreach ($projectProducts as $product)
        {
            $project = $product->getProject();

            // If project is not already in "Funding phase"
            if ($project->getStatus() != '2')
            {
                $now = new \DateTime();
                $submittedAt = $product->getSubmittedAt();
                $dateDiff = $now->diff($submittedAt);
                $dateDiff = $dateDiff->format('%a');

                var_dump($product->getId().' = > '.$dateDiff);

                // First revival (1 day after product creation)
                if ($dateDiff == 1)
                {
                    // Send first revival email
                    $email = \Swift_Message::newInstance()
                        ->setContentType('text/html')
                        ->setSubject($this->getContainer()->get('translator')->trans('An product has been submitted for your project : '.$project->getName()))
                        ->setFrom($this->getContainer()->getParameter('default_email_address'))
                        ->setTo(array($project->getUser()->getEmail() => $project->getUser()))
                        ->setBody(
                            $this->getContainer()->get('templating')->render('LittleBigJoeFrontendBundle:Email:first_revival_project.html.twig', array(
                                'user'    => $project->getUser(),
                                'project' => $project,
                                'url'     => $this->getContainer()->getParameter('default_url')
                            ), 'text/html')
                        );
                    $this->getContainer()->get('mailer')->send($email);
                }
                // Second revival (3 days after product creation)
                else if ($dateDiff == 3)
                {
                    // Send second revival email
                    $email = \Swift_Message::newInstance()
                        ->setContentType('text/html')
                        ->setSubject($this->getContainer()->get('translator')->trans('An product has been submitted for your project : '.$project->getName()))
                        ->setFrom($this->getContainer()->getParameter('default_email_address'))
                        ->setTo(array($project->getUser()->getEmail() => $project->getUser()))
                        ->setBody(
                            $this->getContainer()->get('templating')->render('LittleBigJoeFrontendBundle:Email:second_revival_project.html.twig', array(
                                'user'    => $project->getUser(),
                                'project' => $project,
                                'url'     => $this->getContainer()->getParameter('default_url')
                            ), 'text/html')
                        );
                    $this->getContainer()->get('mailer')->send($email);
                }
                // Third and last revival (6 days after product creation)
                else if ($dateDiff == 6)
                {
                    // Send second revival email
                    $email = \Swift_Message::newInstance()
                        ->setContentType('text/html')
                        ->setSubject($this->getContainer()->get('translator')->trans('An product has been submitted for your project : '.$project->getName()))
                        ->setFrom($this->getContainer()->getParameter('default_email_address'))
                        ->setTo(array($project->getUser()->getEmail() => $project->getUser()))
                        ->setBody(
                            $this->getContainer()->get('templating')->render('LittleBigJoeFrontendBundle:Email:third_revival_project.html.twig', array(
                                'user'    => $project->getUser(),
                                'project' => $project,
                                'url'     => $this->getContainer()->getParameter('default_url')
                            ), 'text/html')
                        );
                    $this->getContainer()->get('mailer')->send($email);
                }
                // Automatically validate the product
                else if ($dateDiff > 7)
                {
                    $product->setValidatedAt(new \DateTime());
                    $em->persist($product);

                    $project->setStatus('2');
                    $em->persist($project);

                    $em->flush();

                    $totalUpdatedProjects++;
                }
            }

            $progress->advance();
        }

        // End of import
        $progress->finish();
        $updatedMessages = array($totalUpdatedProjects . ' project(s) updated !', $totalUpdatedProjects . ' project(s) has been updated in database');
        $formattedBlock = $formatter->formatBlock($updatedMessages, 'comment', true);
        $output->writeln($formattedBlock);

        // Make sure emails are sended, even in DEV environment
        $transport = $this->getContainer()->get('mailer')->getTransport();
        if (!$transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }

        $spool = $transport->getSpool();
        if (!$spool instanceof \Swift_MemorySpool) {
            return;
        }

        $spool->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
    }
}