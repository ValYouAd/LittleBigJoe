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

        $search = $request->request->get('search');
        $projectsSearch = array();
        $usersSearch = array();

        if (!empty($search))
        {
            $projectsSearch = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findBySearch($search);
            $usersSearch = $em->getRepository('LittleBigJoeFrontendBundle:User')->findBySearch($search);
        }

        return array(
            'search' => $search,
            'projects_search' => $projectsSearch,
            'users_search' => $usersSearch,
        );
    }
}
