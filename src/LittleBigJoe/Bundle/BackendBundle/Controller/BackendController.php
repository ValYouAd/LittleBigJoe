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
            'nbPages' => $em->getRepository('LittleBigJoeCoreBundle:Page')->count(),
            'nbPagesVisible' => $em->getRepository('LittleBigJoeCoreBundle:Page')->count(true),
            'nbPagesNotVisible' => $em->getRepository('LittleBigJoeCoreBundle:Page')->count(false),
            // Users stats
            'nbUsers' => $em->getRepository('LittleBigJoeCoreBundle:User')->count(),
            // Projects stats
            'nbProjects' => $em->getRepository('LittleBigJoeCoreBundle:Project')->count(),
            'nbNotDeletedProjects' => $em->getRepository('LittleBigJoeCoreBundle:Project')->count(false),
            'nbDeletedProjects' => $em->getRepository('LittleBigJoeCoreBundle:Project')->count(true),
            'nbEngagementProjects' => $em->getRepository('LittleBigJoeCoreBundle:Project')->count(null, '1'),
            'nbFundingProjects' => $em->getRepository('LittleBigJoeCoreBundle:Project')->count(null, '2'),
            // Cateogories stats
            'nbCategories' => $em->getRepository('LittleBigJoeCoreBundle:Category')->count(),
            'nbCategoriesVisible' => $em->getRepository('LittleBigJoeCoreBundle:Category')->count(true),
            'nbCategoriesNotVisible' => $em->getRepository('LittleBigJoeCoreBundle:Category')->count(false),
            // Users stats
            'nbBrands' => $em->getRepository('LittleBigJoeCoreBundle:Brand')->count(),
            // Projects contributions stats
            'nbContributions' => $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->count(),
            'nbSucceededContributions' => $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->count(1),
            'nbCompletedContributions' => $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->count(null, 1),
        );
    }
}
