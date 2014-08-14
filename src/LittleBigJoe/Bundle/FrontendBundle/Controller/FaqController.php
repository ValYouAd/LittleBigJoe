<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class FaqController extends Controller
{
    /**
     * Faq page
     *
     * @Route("/faq", name="littlebigjoe_frontendbundle_faq")
     * @Template("LittleBigJoeFrontendBundle:Faq:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('LittleBigJoeCoreBundle:FaqCategory')->findAll(array(), array('name' => 'ASC'));
        $faqs = $em->getRepository('LittleBigJoeCoreBundle:Faq')->findAll(array('isVisible' => true), array('category' => 'ASC'));

        return array(
            'faqs' => $faqs,
            'categories' => $categories
        );
    }
}
