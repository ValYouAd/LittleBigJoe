<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BrandController extends Controller
{
    /**
     * Most desired brands
     *
     * @Route("/most-desired-brands", name="littlebigjoe_frontendbundle_brand_most_desired_projects")
     * @Template("LittleBigJoeFrontendBundle:Brand:brands_list.html.twig")
     */
    public function mostDesiredProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeFrontendBundle:Brand')->findMostDesired(null);

        $paginator = $this->get('knp_paginator');
        $brands = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Most desired brands',
            'brands' => $brands
        );
    }

    /**
     * Specific brand
     *
     * @Route("/brand/{slug}", name="littlebigjoe_frontendbundle_brand_show")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Brand')->findBySlugI18n($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand entity.');
        }

        $currentProjectsCount = $em->getRepository('LittleBigJoeFrontendBundle:Project')->count(null, null, true, null, $entity->getId());
        $likesCount = $em->getRepository('LittleBigJoeFrontendBundle:Project')->countLikes($entity->getId());
        $endedProjectsCount = $em->getRepository('LittleBigJoeFrontendBundle:Project')->count(null, null, false, $entity->getId());
        $amountCount = $em->getRepository('LittleBigJoeFrontendBundle:Project')->countAmount($entity->getId());
        $currentProjects = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findCurrent(8, $entity->getId());
        $endedProjects = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findEnded(4, $entity->getId());
        $favoriteProjects = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findFavorite(2, $entity->getId());

        return array(
            'entity' => $entity,
            'currentProjectsCount' => $currentProjectsCount,
            'likesCount' => $likesCount,
            'endedProjectsCount' => $endedProjectsCount,
            'amountCount' => $amountCount,
            'currentProjects' => $currentProjects,
            'endedProjects' => $endedProjects,
            'favoriteProjects' => $favoriteProjects
        );
    }

    /**
     * Current projects for specific brand
     *
     * @Route("/brand/{slug}/current-projects", name="littlebigjoe_frontendbundle_brand_current_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:projects_list.html.twig")
     */
    public function currentProjectsAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Brand')->findBySlugI18n($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand entity.');
        }

        $query = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findCurrent(null, $entity->getId());

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Current projects for ' . $entity->getName(),
            'entity' => $entity,
            'projects' => $projects
        );
    }

    /**
     * Ended projects for specific brand
     *
     * @Route("/brand/{slug}/ended-projects", name="littlebigjoe_frontendbundle_brand_ended_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:projects_list.html.twig")
     */
    public function endedProjectsAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Brand')->findBySlugI18n($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand entity.');
        }

        $query = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findEnded(null, $entity->getId());

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Ended projects for ' . $entity->getName(),
            'entity' => $entity,
            'projects' => $projects
        );
    }
}
