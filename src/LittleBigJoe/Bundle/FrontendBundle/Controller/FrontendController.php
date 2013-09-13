<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class FrontendController extends Controller
{
    /**
     * Default homepage for frontend
     *
     * @Route("/", name="littlebigjoe_frontendbundle_home")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $latestProjects = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findLatest();
        $popularProjects = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findPopular();
        $fundingProjects = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findFunding();
        $latestBrands = $em->getRepository('LittleBigJoeFrontendBundle:Brand')->findLatestByProject();
        $latestProjectContributions = $em->getRepository('LittleBigJoeFrontendBundle:ProjectContribution')->findLatest(3);
        
        return array(
            'latestProjects' => $latestProjects,
            'popularProjects' => $popularProjects,
            'fundingProjects' => $fundingProjects,
            'latestBrands' => $latestBrands,
            'latestProjectContributions' => $latestProjectContributions
        );
    }
    
    /**
     * upload
     *
     * @Route("/upload", name="littlebigjoe_frontendbundle_upload")
     * @Template()
     */
    public function uploadAction()
    {
    		var_dump($this->getRequest());
    }
}
