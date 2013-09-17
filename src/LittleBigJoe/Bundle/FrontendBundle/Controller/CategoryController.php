<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CategoryController extends Controller
{
    /**
     * Specific category
     *
     * @Route("/category/{slug}", name="littlebigjoe_frontendbundle_category_show")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:Category')->findBySlugI18n($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findBy(array('category' => $entity->getId(), 'deletedAt' => null), array('id' => 'DESC'));

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'entity' => $entity,
            'title' => $entity->getName(),
            'projects' => $projects
        );
    }
}
