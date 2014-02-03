<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function topMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $em = $this->container->get('doctrine.orm.entity_manager');

        // Add home link
        $menu->addChild($this->container->get('translator')->trans('Home'), array('route' => 'littlebigjoe_frontendbundle_home'));

        // Add "How does this website work ?" link
        $pageId = $this->container->getParameter('howdoesitwork_page_id');
        if (!empty($pageId) && $page = $em->getRepository('LittleBigJoeCoreBundle:Page')->find($pageId)) {
            $menu->addChild($page->getTitle(), array(
                'route' => 'littlebigjoe_frontendbundle_page_show',
                'routeParameters' => array('slug' => $page->getSlug())
            ));
        }

        // Add "Projects" link
        $projects = $menu->addChild($this->container->get('translator')->trans('Projects'), array('uri' => '#'));
        
        // Add "Discover projects" link
        $projects->addChild($this->container->get('translator')->trans('Discovering projects'), array('route' => 'littlebigjoe_frontendbundle_project'));
        
        // Add "Projects I'm supporting" link
        $projects->addChild($this->container->get('translator')->trans('Projects I\'m supporting'), array('route' => 'littlebigjoe_frontendbundle_project_supported_projects'));
        
        // Add "Projects of users I follow" link
        $projects->addChild($this->container->get('translator')->trans('Projects of users I follow'), array('route' => 'littlebigjoe_frontendbundle_project_users_followed_projects'));
        
        // Add "Projects of brands I follow" link
        $projects->addChild($this->container->get('translator')->trans('Projects of brands I follow'), array('route' => 'littlebigjoe_frontendbundle_project_brands_followed_projects'));
        
        // Add "Launch my project" link
        $menu->addChild($this->container->get('translator')->trans('Launch my project'), array('route' => 'littlebigjoe_frontendbundle_project_create_project'));

        return $menu;
    }

    public function typeMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $em = $this->container->get('doctrine.orm.entity_manager');

        // Add "Popular projects" link
        $menu->addChild($this->container->get('translator')->trans('Popular projects'), array('route' => 'littlebigjoe_frontendbundle_project_popular_projects'));

        // Add "Top funded projects" link
        $menu->addChild($this->container->get('translator')->trans('Top funded projects'), array('route' => 'littlebigjoe_frontendbundle_project_top_funded_projects'));

        // Add "Projects nearing completion" link
        $menu->addChild($this->container->get('translator')->trans('Projects nearing completion'), array('route' => 'littlebigjoe_frontendbundle_project_almost_ending_projects'));

        return $menu;
    }

    public function brandMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $em = $this->container->get('doctrine.orm.entity_manager');

        // Add "Most desired" link
        $menu->addChild($this->container->get('translator')->trans('Most desired'), array('route' => 'littlebigjoe_frontendbundle_brand_most_desired_projects'));

        // Add "Latest projects" link
        $menu->addChild($this->container->get('translator')->trans('Latest projects'), array('route' => 'littlebigjoe_frontendbundle_project_latest_projects'));

        // Add "Projects nearing completion" link
        $menu->addChild($this->container->get('translator')->trans('Projects nearing completion'), array('route' => 'littlebigjoe_frontendbundle_project_almost_ending_projects'));

        // Add "Best funded" link
        $menu->addChild($this->container->get('translator')->trans('Top funded projects'), array('route' => 'littlebigjoe_frontendbundle_project_top_funded_projects'));

        return $menu;
    }

    public function categoryMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $em = $this->container->get('doctrine.orm.entity_manager');
        $categories = $em->getRepository('LittleBigJoeCoreBundle:Category')->findBy(array('isVisible' => true));

        // Add categories
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $menu->addChild($category->getName(), array(
                    'route' => 'littlebigjoe_frontendbundle_category_show',
                    'routeParameters' => array('slug' => $category->getSlug())
                ));
            }
        }

        return $menu;
    }
}