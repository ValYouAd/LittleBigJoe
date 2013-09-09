<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PageController extends Controller
{
    /**
     * Specific faq
     *
     * @Route("/faq/{slug}", name="littlebigjoe_frontendbundle_faq_show")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Faq')->findBySlugI18n($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Faq entity.');
        }

        return array(
            'entity' => $entity
        );
    }
}
