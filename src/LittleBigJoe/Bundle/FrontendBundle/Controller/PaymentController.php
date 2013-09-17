<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectLike;

/**
 * Payment controller.
 *
 * @Route("/payment")
 */
class PaymentController extends Controller
{
    /**
     * Allows user to select a reward and pay
     *
     * @Route("/", name="littlebigjoe_frontendbundle_payment_project")
     * @Method("POST")
     * @Template()
     */
    public function indexAction()
    {    		
    		$em = $this->getDoctrine()->getManager();
    		$projectId = (int)$this->get('request')->request->get('projectId');

    		$currentUser = $this->get('security.context')->getToken()->getUser();
				// If the current user is not logged, redirect him to login page
				if (!is_object($currentUser))
				{
						$this->get('session')->getFlashBag()->add(
								'notice',
								'You must be logged in to contribute to this project'
						);
						
						return $this->redirect($this->generateUrl('fos_user_security_login'));
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
    		    		 		
				return array(
						'project' => $project
				);
    }
}
