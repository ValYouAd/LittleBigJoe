<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BackendController extends Controller
{
    /**
     * Default homepage for backend
     *
     * @Route("/", name="littlebigjoe_backendbundle_home")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        return array(
            // Pages stats
            'nbPages' => $em->getRepository('LittleBigJoeFrontendBundle:Page')->count(),
            'nbPagesVisible' => $em->getRepository('LittleBigJoeFrontendBundle:Page')->count(true),
            'nbPagesNotVisible' => $em->getRepository('LittleBigJoeFrontendBundle:Page')->count(false),
            // Users stats
            'nbUsers' => $em->getRepository('LittleBigJoeFrontendBundle:User')->count(),
            // Projects stats
            'nbProjects' => $em->getRepository('LittleBigJoeFrontendBundle:Project')->count(),
            'nbNotDeletedProjects' => $em->getRepository('LittleBigJoeFrontendBundle:Project')->count(false),
            'nbDeletedProjects' => $em->getRepository('LittleBigJoeFrontendBundle:Project')->count(true),
            'nbEngagementProjects' => $em->getRepository('LittleBigJoeFrontendBundle:Project')->count(null, 1),
            'nbFundingProjects' => $em->getRepository('LittleBigJoeFrontendBundle:Project')->count(null, 2),
            // Cateogories stats
            'nbCategories' => $em->getRepository('LittleBigJoeFrontendBundle:Category')->count(),
            'nbCategoriesVisible' => $em->getRepository('LittleBigJoeFrontendBundle:Category')->count(true),
            'nbCategoriesNotVisible' => $em->getRepository('LittleBigJoeFrontendBundle:Category')->count(false),
            // Users stats
            'nbBrands' => $em->getRepository('LittleBigJoeFrontendBundle:Brand')->count(),
            // Projects contributions stats
            'nbContributions' => $em->getRepository('LittleBigJoeFrontendBundle:ProjectContribution')->count(),
            'nbSucceededContributions' => $em->getRepository('LittleBigJoeFrontendBundle:ProjectContribution')->count(1),
            'nbCompletedContributions' => $em->getRepository('LittleBigJoeFrontendBundle:ProjectContribution')->count(null, 1),
        );
    }
}
