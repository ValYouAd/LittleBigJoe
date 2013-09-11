<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SearchController extends Controller
{
    /**
     * Default handler for search
     *
     * @Route("/search", name="littlebigjoe_frontendbundle_search")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $search = $request->query->get('term', '');
				
        $projectsSearch = array();
        $usersSearch = array();

        if (!empty($search))
        {
            $projectsSearch = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findBySearch($search);
            $usersSearch = $em->getRepository('LittleBigJoeFrontendBundle:User')->findBySearch($search);
        }
        
        $paginator = $this->get('knp_paginator');
        // Take the max number of results to generate the global pagination
        $pagination = $paginator->paginate(
        		((sizeof($projectsSearch) > sizeof($usersSearch)) ? $projectsSearch : $usersSearch),
        		$this->get('request')->query->get('page', 1),
        		2
        );
        // Generate pagination for entities
        $projects = $paginator->paginate(
        		$projectsSearch,
        		$this->get('request')->query->get('page', 1),
        		2
        );
        $users = $paginator->paginate(
        		$usersSearch,
        		$this->get('request')->query->get('page', 1),
        		2
        );
        
        return array(
        		'search' => $search,
        		'projects' => $projects,
        		'users' => $users,
        		'pagination' => $pagination
        );
    }
}
