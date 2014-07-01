<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectLike;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Payment controller.
 *
 * @Route("/payment")
 */
class PaymentController extends Controller
{
    /**
     * Check if all necessary data are set
     */
    public function preCheck(Request $request)
    {
            $em = $this->getDoctrine()->getManager();
            $formData = $this->get('request')->request->all();

            // Retrieve the project id that was sent previously
            if (!empty($formData['form']['projectId']))
            {
                    $projectId = $formData['form']['projectId'];
                    $this->get('session')->set('projectId', $projectId);
            }
            // If project id is in session
            else if ($this->get('session')->get('projectId') != null)
            {
                    $projectId = $this->get('session')->get('projectId');
            }
            else
            {
                    $projectId = 0;
            }

            // If it's not a correct project id
            if (empty($projectId))
            {
                    return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_home'));
            }

            $project = $em->getRepository('LittleBigJoeCoreBundle:Project')->find($projectId);

            // If the project doesn't exist
            if (empty($project))
            {
                    return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_home'));
            }

            $currentUser = $this->get('security.context')->getToken()->getUser();
            // If the current user is not logged, redirect him to login page
            if (!is_object($currentUser))
            {
                    $this->get('session')->getFlashBag()->add(
                            'notice',
                            'You must be logged in to contribute to this project'
                    );

                    // Force base url to make sure environment is not specified in the URL
                    $this->get('router')->getContext()->setBaseUrl('');
                    $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_project_show', array('id' => $project->getId(), 'slug' => $project->getSlug())));
                    return $this->redirect($this->generateUrl('fos_user_security_login'));
            }

            return array(
                    'project' => $project,
                    'user' => $currentUser,
                    'form' => $formData
            );
    }
		
		
    /**
     * Allows user to select a reward and pay
     *
     * @Route("/", name="littlebigjoe_frontendbundle_payment_project")
     * @Method("POST")
     * @Template()
     */
    public function indexAction(Request $request)
    {    		
    		$em = $this->getDoctrine()->getManager();
    		$data = $this->preCheck($request);
    		
    		// Redirect eventually
    		if (is_object($data) && $data instanceof RedirectResponse)
    		{
    				return $data;
    		}
    		
    		$rewards = $em->getRepository('LittleBigJoeCoreBundle:ProjectReward')->findAvailable($data['project']->getId());
    		$unavailableRewards = $em->getRepository('LittleBigJoeCoreBundle:ProjectReward')->findUnavailable($data['project']->getId(), $data['user']->getId());
    		$faqs = $em->getRepository('LittleBigJoeCoreBundle:Faq')->findBy(array('isVisible' => true));
            $generalConditionsPageId = $this->container->getParameter('cgu_page_id');
            $generalConditionsPage = $em->getRepository('LittleBigJoeCoreBundle:Page')->find($generalConditionsPageId);

    		// If there's no project rewards
    		if (empty($rewards))
    		{
    				return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_home'));
    		}
    		
				return array(
						'project' => $data['project'],
                        'generalConditionsPage' => $generalConditionsPage,
						'faqs' => $faqs,
						'rewards' => $rewards,
						'unavailableRewards' => $unavailableRewards
				);
    }
    
    /**
     * Process payment data and redirect to bank interface
     *
     * @Route("/process", name="littlebigjoe_frontendbundle_payment_process")
     * @Method("POST")
     */
    public function processAction(Request $request)
    {
    		$em = $this->getDoctrine()->getManager();
	    	$data = $this->preCheck($request);
	    	$rewardId = 0;
	    	$amountToPay = 0;
	    	
	    	// Redirect eventually
	    	if (is_object($data) && $data instanceof RedirectResponse)
	    	{
                return $data;
	    	}

            // Cast the entered amount
            $data['form']['amount'] = (int)$data['form']['amount'];

	    	// If user has enterd a specific amount
	    	if (!empty($data['form']['amount']) && $data['form']['amount'] >= 1)
	    	{
                $rewards = $em->getRepository('LittleBigJoeCoreBundle:ProjectReward')->findAvailable($data['project']->getId());
                $unavailableRewards = $em->getRepository('LittleBigJoeCoreBundle:ProjectReward')->findUnavailable($data['project']->getId(), $data['user']->getId());

                // Retrieve the reward that is associated to the amount
                foreach ($rewards as $reward)
                {
                    if ($reward->getAmount() <= $data['form']['amount'] && !in_array($reward->getId(), $unavailableRewards))
                    {
                        // Get the associated reward id
                        $rewardId = $reward->getId();
                    }
                }

                // If no rewards has been found
                if (empty($rewardId))
                {
                    $rewardId = $rewards[0]->getId();
                }

                // Override default reward amount
                $amountToPay = (float)$data['form']['amount'];
	    	}
            // If user has selected a reward
	    	else if (!empty($data['form']['rewards'][0]))
	    	{
	    		$rewardId = $data['form']['rewards'][0];
	    	}
	    	// If user has not selected a reward, and not entered specific amount
	    	else
	    	{
	    		return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_home').'?norewards');
	    	}	

	    	// Make sure the reward id we get is correct
	    	$reward = $em->getRepository('LittleBigJoeCoreBundle:ProjectReward')->findOneBy(array('project' => $data['project'], 'id' => $rewardId));

	    	// If the reward doesn't exist and amount not specified
	    	if (empty($reward) && $amountToPay == 0)
	    	{
                return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_home').'?notexistingrewards');
	    	}

	    	// Create contribution
	    	$contribution = new ProjectContribution();
	    	$contribution->setProject($data['project']);
	    	$contribution->setUser($data['user']);
	    	$contribution->setIsAnonymous(($request->request->get('anonymous')) ? true : false);
	    	// Only set reward, if there's one selected previously
	    	if (!empty($reward))
	    	{
	    	    $contribution->setReward($reward);
	    	}   		
	    	$em->persist($contribution);
	    	$em->flush();
	    		    	
	    	// Create contribution in MangoPay
	    	$returnUrl = $this->getRequest()->getSchemeAndHttpHost().$this->generateUrl('littlebigjoe_frontendbundle_payment_confirmation');
	    	$api = $this->container->get('little_big_joe_mango_pay.api');	    
	    	
	    	// If we must use the default reward amount
	    	if (empty($amountToPay))
	    	{
                $amountToPay = $reward->getAmount();
	    	}

	    	$mangopayContribution = $api->createContribution($data['project']->getMangopayWalletId(), $data['user']->getMangopayUserId(), $amountToPay*100, $returnUrl, $contribution->getId(), null, null, null, null, $data['user']->getDefaultLanguage(), null, null);
            //var_dump($mangopayContribution);die('**');
            if (!empty($mangopayContribution))
	    	{
                if (!empty($mangopayContribution->ID))
                {
                    $contribution->setMangopayContributionId($mangopayContribution->ID);
                    $this->get('session')->set('contributionId', $mangopayContribution->ID);
                }
                if (!empty($mangopayContribution->Amount))
                {
                    $contribution->setMangopayAmount($mangopayContribution->Amount/100);
                }
                if (!empty($mangopayContribution->IsSucceeded))
                {
                    $contribution->setMangopayIsSucceeded($mangopayContribution->IsSucceeded);
                }
                if (!empty($mangopayContribution->IsCompleted))
                {
                    $contribution->setMangopayIsCompleted($mangopayContribution->IsCompleted);
                }
                if (!empty($mangopayContribution->CreationDate))
                {
                    $contribution->setMangopayCreatedAt(new \DateTime('@'.$mangopayContribution->CreationDate));
                }
                if (!empty($mangopayContribution->UpdateDate))
                {
                    $contribution->setMangopayUpdatedAt(new \DateTime('@'.$mangopayContribution->UpdateDate));
                }
                $em->persist($contribution);
                $em->flush();

                // Redirect user to payment page
                return $this->redirect($mangopayContribution->PaymentURL);
	    	}
	    	else
    		{
                return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_home').'?failmango');
            }
    }
    
    /**
     * Payment confirmation
     *
     * @Route("/confirmation", name="littlebigjoe_frontendbundle_payment_confirmation")
     * @Template()
     */
    public function confirmationAction(Request $request)
    {
	    	$em = $this->getDoctrine()->getManager();
	    	$data = $this->preCheck($request);
	    	$status = 'KO';

	    	// Redirect eventually
	    	if (is_object($data) && $data instanceof RedirectResponse)
	    	{
	    			return $data;
	    	}
	    	
	    	if ($this->get('session')->get('contributionId') != null)
	    	{
	    			// Fetch contribution from Mangopay to check if payment is OK
	    			$contribution = $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->findOneBy(array('mangopayContributionId' => $this->get('session')->get('contributionId')));
	    			$api = $this->container->get('little_big_joe_mango_pay.api');
	    			$mangopayContribution = $api->fetchContribution($this->get('session')->get('contributionId'));
	    			if (!empty($mangopayContribution) && !empty($contribution))
	    			{
			    			if (!empty($mangopayContribution->IsSucceeded))
				    		{
				    				$contribution->setMangopayIsSucceeded($mangopayContribution->IsSucceeded);
				    		}
				    		if (!empty($mangopayContribution->IsCompleted))
				    		{
				    				$contribution->setMangopayIsCompleted($mangopayContribution->IsCompleted);
				    		}	
				    		if (!empty($mangopayContribution->Error))
				    		{
				    				$contribution->setMangopayError($mangopayContribution->Error->TechnicalMessage);
				    		}				    		
				    		if (!empty($mangopayContribution->AnswerCode))
				    		{
				    				$contribution->setMangopayAnswerCode($mangopayContribution->AnswerCode);
				    		}
				    		if (!empty($mangopayContribution->CreationDate))
				    		{
				    				$contribution->setMangopayCreatedAt(new \DateTime('@'.$mangopayContribution->CreationDate));
				    		}
				    		if (!empty($mangopayContribution->UpdateDate))
				    		{
				    				$contribution->setMangopayUpdatedAt(new \DateTime('@'.$mangopayContribution->UpdateDate));
				    		}				    		
				    		
				    		if ($mangopayContribution->IsSucceeded == true && $mangopayContribution->IsCompleted == true)
				    		{
				    				$status = 'OK';
				    				
				    				// Make sure the PDF dir is created
				    				$pdfPath = $this->container->getParameter('kernel.root_dir').'/../web/uploads/pdfs/';
				    				if (!file_exists($pdfPath))
				    				{
				    						mkdir($pdfPath, 0755);
				    				}
				    								    				
				    				$pdfName = 'invoice_'.sha1($contribution->getMangopayContributionId().uniqid(mt_rand(), true)).'.pdf';
				    				$handle = fopen($pdfPath.$pdfName, 'a+');
				    				if ($handle)
				    				{
				    						// Generate pdf file
					    					$html2pdf = $this->get('html2pdf')->get();
					    					$html2pdf->WriteHTML($this->container->get('templating')->render('LittleBigJoeFrontendBundle:Pdf:payment_invoice.pdf.twig', array(
					    							'user' => $data['user'],
					    							'project' => $data['project'],
					    							'contribution' => $contribution,
					    							'url' => $this->container->get('request')->getSchemeAndHttpHost()
					    					)));
				    						$html2pdf->Output($pdfPath.$pdfName, 'F');
				    				}
				    				fclose($handle);
				    				
				    				// Stock invoice name in database
				    				$contribution->setInvoice($pdfName);
				    				
				    				// Send payment confirmation email
				    				$email = \Swift_Message::newInstance()
									    				->setContentType('text/html')
									    				->setSubject($this->container->get('translator')->trans('Thanks for your contribution'))
									    				->setFrom($this->container->getParameter('default_email_address'))
									    				->setTo(array($data['user']->getEmail() => $data['user']))
									    				->attach(\Swift_Attachment::fromPath($pdfPath.$pdfName))
									    				->setBody(
									    						$this->container->get('templating')->render('LittleBigJoeFrontendBundle:Email:payment_confirmation.html.twig', array(
									    								'user' => $data['user'],
									    								'project' => $data['project'],
									    								'contribution' => $contribution,
									    								'url' => $this->container->get('request')->getSchemeAndHttpHost()
									    						), 'text/html')
									    				);
				    				$this->container->get('mailer')->send($email);
				    				
				    				// Send new contribution email
				    				$email = \Swift_Message::newInstance()
									    				->setContentType('text/html')
									    				->setSubject($this->container->get('translator')->trans('New contribution'))
									    				->setFrom($this->container->getParameter('default_email_address'))
									    				->setTo(array($data['project']->getUser()->getEmail() => $data['project']->getUser()))
									    				->setBody(
									    						$this->container->get('templating')->render('LittleBigJoeFrontendBundle:Email:new_contribution.html.twig', array(
									    								'user' => $data['project']->getUser(),
									    								'contributor' => $data['user'],
									    								'project' => $data['project'],
									    								'contribution' => $contribution,
									    								'url' => $this->container->get('request')->getSchemeAndHttpHost()
									    						), 'text/html')
									    				);
				    				$this->container->get('mailer')->send($email);
				    		}
				    		
				    		$em->persist($contribution);
				    		$em->flush();
	    			}
	    	}
    	
    		return array(
    				'status' => $status    				
    		);
    }
}
