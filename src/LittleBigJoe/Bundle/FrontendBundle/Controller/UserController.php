<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
    /**
     * Specific user profile
     *
     * @Route("/user/{id}", name="littlebigjoe_frontendbundle_user_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        
        $projectContributions = $entity->getContributions();
        $distinctProjectsIds = array();
        $distinctProjects = array();
        if (!empty($projectContributions))
        {
        		foreach ($projectContributions as $projectContribution)
        		{
        				if ($projectContribution->getIsAnonymous() == false && !in_array($projectContribution->getProject()->getId(), $distinctProjectsIds))
        				{
        						$distinctProjectsIds[] = $projectContribution->getProject()->getId();
        						$distinctProjects[] = $projectContribution->getProject();
        				}        		
        		}
        }

        return array(
            'entity' => $entity,
        	'distinctProjects' => $distinctProjects
        );
    }
    
    /**
     * Notifications feed
     *
     * @Route("/my-news", name="littlebigjoe_frontendbundle_user_news")
     * @Template()
     */
    public function newsAction()
    {
        $em = $this->getDoctrine()->getManager();
    
        $currentUser = $this->get('security.context')->getToken()->getUser();
        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser))
        {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to access to your news'
            );
            	
            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_user_news'));
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }        
    
        // Get notifications for current user
        $notifications = $em->getRepository('LittleBigJoeCoreBundle:Notification')->findBy(array('user' => $currentUser), array('createdAt' => 'desc'));
        
        // Set notifications has viewed
        if (!empty($notifications))
        {
            foreach ($notifications as $notification)
            {
                if (!$notification->getViewed())
                {
                    $notification->setViewed(true);
                    $em->persist($notification);
                    $em->flush();
                }
            }
        }
        
        return array(
            'entity' => $currentUser,
            'notifications' => $notifications,
        );
    }
}
