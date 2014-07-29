<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use LittleBigJoe\Bundle\FrontendBundle\Form\ProfileDeletionFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * Projects list
     *
     * @Route("/my-projects", name="littlebigjoe_frontendbundle_user_projects")
     * @Template()
     */
    public function projectsAction()
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
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_user_projects'));
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        return array(
            'entity' => $currentUser,
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

    /**
     * User deletion page
     *
     * @Route("/delete-account", name="littlebigjoe_frontendbundle_user_delete_account")
     * @Template()
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $currentUser = $this->get('security.context')->getToken()->getUser();
        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser))
        {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to delete your account'
            );

            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_user_news'));
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $form = $this->get('form.factory')->create(new ProfileDeletionFormType());

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            if ($form->isValid())
            {
                $em->remove($currentUser);
                $em->flush();

                return new RedirectResponse($this->generateUrl('littlebigjoe_frontendbundle_home'));
            }
        }

        return array(
            'entity' => $currentUser,
            'form' => $form->createView()
        );
    }
}
