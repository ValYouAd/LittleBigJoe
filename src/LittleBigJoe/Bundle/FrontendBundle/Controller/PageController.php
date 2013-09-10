<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PageController extends Controller
{
    /**
     * Specific page
     *
     * @Route("/page/{slug}", name="littlebigjoe_frontendbundle_page_show")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Page')->findBySlugI18n($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        return array(
            'entity' => $entity
        );
    }
}
