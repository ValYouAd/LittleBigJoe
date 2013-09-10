<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ProjectController extends Controller
{
    /**
     * Default handler for project
     *
     * @Route("/projects", name="littlebigjoe_frontendbundle_project")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $favoriteProjects = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findFavorite();
        $popularProjects = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findPopular(4, '-7 days');
        $recentlyUpdatedProjects = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findRecentlyUpdated();

        return array(
            'favoriteProjects' => $favoriteProjects,
            'popularProjects' => $popularProjects,
            'recentlyUpdatedProjects' => $recentlyUpdatedProjects
        );
    }

    /**
     * Latest projects
     *
     * @Route("/latest-projects", name="littlebigjoe_frontendbundle_project_latest_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:projects_list.html.twig")
     */
    public function latestProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findLatest(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Latest projects',
            'projects' => $projects
        );
    }

    /**
     * Popular projects
     *
     * @Route("/popular-projects", name="littlebigjoe_frontendbundle_project_popular_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:projects_list.html.twig")
     */
    public function popularProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findPopular(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Popular projects',
            'projects' => $projects
        );
    }

    /**
     * Popular week projects
     *
     * @Route("/popular-projects-this-week", name="littlebigjoe_frontendbundle_project_popular_week_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:projects_list.html.twig")
     */
    public function popularWeekProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findPopular(null, '-7 days');

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Popular week projects',
            'projects' => $projects
        );
    }

    /**
     * Funding projects
     *
     * @Route("/funding-projects", name="littlebigjoe_frontendbundle_project_funding_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:projects_list.html.twig")
     */
    public function fundingProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findFunding(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Projects to fund',
            'projects' => $projects
        );
    }

    /**
     * Top funded projects
     *
     * @Route("/top-funded-projects", name="littlebigjoe_frontendbundle_project_top_funded_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:projects_list.html.twig")
     */
    public function topFundedProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findTopFunded(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Top funded projects',
            'projects' => $projects
        );
    }

    /**
     * Almost ending projects
     *
     * @Route("/almost-ending-projects", name="littlebigjoe_frontendbundle_project_almost_ending_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:projects_list.html.twig")
     */
    public function almostEndingProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findAlmostEnding(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Almost ending projects',
            'projects' => $projects
        );
    }

    /**
     * Favorite projects
     *
     * @Route("/favorite-projects", name="littlebigjoe_frontendbundle_project_favorite_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:projects_list.html.twig")
     */
    public function favoriteProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findFavorite(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Favorite projects',
            'projects' => $projects
        );
    }

    /**
     * Recently updated projects
     *
     * @Route("/recently-updated-projects", name="littlebigjoe_frontendbundle_project_recently_updated_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:projects_list.html.twig")
     */
    public function recentlyUpdatedProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findRecentlyUpdated(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Recently updated projects',
            'projects' => $projects
        );
    }

    /**
     * Create new project
     *
     * @Route("/launch-my-project", name="littlebigjoe_frontendbundle_project_new")
     * @Template()
     */
    public function newAction()
    {
        return array();
    }

    /**
     * Specific project
     *
     * @Route("/project/{slug}", name="littlebigjoe_frontendbundle_project_show")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Project')->findBySlugI18n($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        return array(
            'entity' => $entity,
            'current_date' => new \Datetime()
        );
    }
}
