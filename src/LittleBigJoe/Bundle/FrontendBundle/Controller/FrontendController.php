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

        $latestProjects = $em->getRepository('LittleBigJoeCoreBundle:Project')->findLatest();
        $popularProjects = $em->getRepository('LittleBigJoeCoreBundle:Project')->findPopular();
        $fundingProjects = $em->getRepository('LittleBigJoeCoreBundle:Project')->findFunding();
        $latestBrands = $em->getRepository('LittleBigJoeCoreBundle:Brand')->findLatestByProject();
        $latestProjectContributions = $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->findLatest(3);
        
        return array(
            'latestProjects' => $latestProjects,
            'popularProjects' => $popularProjects,
            'fundingProjects' => $fundingProjects,
            'latestBrands' => $latestBrands,
            'latestProjectContributions' => $latestProjectContributions
        );
    }
}
