<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use LittleBigJoe\Bundle\FrontendBundle\Entity\ProjectLike;

/**
 * Ajax controller.
 *
 * @Route("/ajax", options={ "i18n": false })
 */
class AjaxController extends Controller
{
    /**
     * Like a project
     *
     * @Route("/like-project", name="littlebigjoe_frontendbundle_ajax_like_project")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function likeProjectAction()
    {    		
    		$em = $this->getDoctrine()->getManager();
    		$projectId = (int)$this->get('request')->request->get('projectId');

    		$currentUser = $this->get('security.context')->getToken()->getUser();
				// If the current user is not logged, redirect him to login page
				if (!is_object($currentUser))
				{
						$this->get('session')->getFlashBag()->add(
								'notice',
								'You must be logged in to like this project'
						);
						
						return new JsonResponse(array('status' => 'KO USER'));
				}
    		
    		// If it's not a correct project id
    		if (empty($projectId))
    		{
						return new JsonResponse(array('status' => 'KO ID'));
    		}
    		
    		$project = $em->getRepository('LittleBigJoeFrontendBundle:Project')->find($projectId);
    		
    		// If the project doesn't exist
    		if (empty($project))
    		{
    				return new JsonResponse(array('status' => 'KO PROJECT'));
    		}    		
    		
    		$projectLike = new ProjectLike();
    		$projectLike->setProject($project);
    		$projectLike->setUser($currentUser);
    		
    		// Save like in DB
    		$em->persist($projectLike);
    		$em->flush();
    		
    		// Make sure no code is executed after it
    		return new JsonResponse(array('status' => 'OK'));
    		exit;
    }
}
