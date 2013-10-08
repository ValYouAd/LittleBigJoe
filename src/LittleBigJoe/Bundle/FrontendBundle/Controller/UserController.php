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
        				if ($projectContribution->getAnonymous() == false && !in_array($projectContribution->getProject()->getId(), $distinctProjectsIds))
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
}
