<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;

class Builder extends ContainerAware
{
    public function topMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav main-nav-menu');
        $em = $this->container->get('doctrine.orm.entity_manager');

        // Add "How does this website work ?" link
        $pageId = $this->container->getParameter('howdoesitwork_page_id');
        if (!empty($pageId) && $page = $em->getRepository('LittleBigJoeCoreBundle:Page')->find($pageId))
        {
            $labelFirst = $this->container->get('translator')->trans('How does');
            $labelSecond = $this->container->get('translator')->trans('it work ?');

            $menu->addChild('<div class="blue-catch">'.$labelFirst.'</div><div class="white-catch">'.$labelSecond.'</div>', array(
                'route' => 'littlebigjoe_frontendbundle_page_show',
                'routeParameters' => array('slug' => $page->getSlug())
            ))
            ->setExtra('safe_label', true)
            ->setAttribute('class', 'main-nav-item');
        }

        // Add "Projects" link
        $labelFirst = $this->container->get('translator')->trans('Discovering');
        $labelSecond = $this->container->get('translator')->trans('projects');
        $projects = $menu->addChild('<div class="blue-catch">'.$labelFirst.'</div><div class="white-catch">'.$labelSecond.'</div>', array(
            'uri' => '#'
        ))
        ->setExtra('safe_label', true)
        ->setAttribute('class', 'dropdown main-nav-item')
        ->setLinkAttribute('data-toggle', 'dropdown')
        ->setLinkAttribute('class', 'dropdown-toggle');

        $projects->setChildrenAttribute('class', 'dropdown-menu');

        // Add "All brands" link
        $projects->addChild($this->container->get('translator')->trans('All brands'), array('route' => 'littlebigjoe_frontendbundle_brand'))
            ->setAttribute('class', 'light-item');

        // Add "Discover projects" link
        $projects->addChild($this->container->get('translator')->trans('All projects'), array('route' => 'littlebigjoe_frontendbundle_project'))
            ->setAttribute('class', 'dark-item');
        
        // Add "Projects I'm supporting" link
        $projects->addChild($this->container->get('translator')->trans('Projects I\'m supporting'), array('route' => 'littlebigjoe_frontendbundle_project_supported_projects'))
            ->setAttribute('class', 'light-item');
        
        // Add "Projects of users I follow" link
        $projects->addChild($this->container->get('translator')->trans('Projects of users I follow'), array('route' => 'littlebigjoe_frontendbundle_project_users_followed_projects'))
            ->setAttribute('class', 'dark-item');

        // Add "Projects of brands I follow" link
        $projects->addChild($this->container->get('translator')->trans('Projects of brands I follow'), array('route' => 'littlebigjoe_frontendbundle_project_brands_followed_projects'))
            ->setAttribute('class', 'light-item');
        
        // Add "Launch my project" link
        $labelFirst = $this->container->get('translator')->trans('Launch');
        $labelSecond = $this->container->get('translator')->trans('my project');
        $menu->addChild('<div class="blue-catch">'.$labelFirst.'</div><div class="white-catch">'.$labelSecond.'</div>', array(
            'route' => 'littlebigjoe_frontendbundle_project_create_project_preamble'
        ))
        ->setExtra('safe_label', true)
        ->setAttribute('class', 'main-nav-item');

        return $menu;
    }

    public function typeMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'kinds-nav');
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
        $menu->setChildrenAttribute('class', 'brands-nav');
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
        $menu->setChildrenAttribute('class', 'categories-nav');
        $em = $this->container->get('doctrine.orm.entity_manager');
        $categories = $em->getRepository('LittleBigJoeCoreBundle:Category')->findBy(array('isVisible' => true), array('name' => 'ASC'));

        // Add categories
        if (!empty($categories)) {
            foreach ($categories as $category) {

                $nbProjects = $em->getRepository('LittleBigJoeCoreBundle:Project')->count(false, null, null, null, $category->getId());
                $categoryName = $category->getName().' <span class="cat-count">'.$nbProjects.'</span>';

                $menu->addChild($categoryName, array(
                    'route' => 'littlebigjoe_frontendbundle_category_show',
                    'routeParameters' => array('slug' => $category->getSlug())
                ))
                ->setExtra('safe_label', true);
            }
        }

        return $menu;
    }

    public function footerCategoryMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $em = $this->container->get('doctrine.orm.entity_manager');
        $categories = $em->getRepository('LittleBigJoeCoreBundle:Category')->findBy(array('isVisible' => true));

        // Add categories
        if (!empty($categories)) {
            // Default
            $size = 1;
            if (sizeof($categories) > 3)
            {
                $size = ceil(sizeof($categories)/3);
            }

            $categories = array_chunk((array)$categories, $size, true);

            if (is_array($categories))
            {
                foreach ($categories as $key => $items) {
                    $subMenu[$key] = $menu->addChild($key);

                    foreach ($items as $key_category => $category) {
                        $subMenu{$key}->addChild($category->getName(), array(
                            'route' => 'littlebigjoe_frontendbundle_category_show',
                            'routeParameters' => array('slug' => $category->getSlug())
                        ));
                    }
                }
            }
        }

        return $menu;
    }

    public function footerAboutMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $em = $this->container->get('doctrine.orm.entity_manager');

        $pageIds = $this->container->getParameter('footer_about_page_ids');
        $pages = $em->getRepository('LittleBigJoeCoreBundle:Page')->findAll();
        if (!empty($pages))
        {
            foreach ($pages as $page)
            {
                if (in_array($page->getId(), $pageIds))
                {
                    $menu->addChild($page->getTitle(), array(
                        'route' => 'littlebigjoe_frontendbundle_page_show',
                        'routeParameters' => array('slug' => $page->getSlug())
                    ));
                }
            }
        }
        $menu->addChild('Contact', array(
            'route' => 'littlebigjoe_frontendbundle_contact',
            'routeParameters' => array()
        ));

        return $menu;
    }

    public function footerUtilsMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $em = $this->container->get('doctrine.orm.entity_manager');

        $pageIds = $this->container->getParameter('footer_utils_page_ids');
        $pages = $em->getRepository('LittleBigJoeCoreBundle:Page')->findAll();
        if (!empty($pages))
        {
            foreach ($pages as $page)
            {
                if (in_array($page->getId(), $pageIds))
                {
                    $menu->addChild($page->getTitle(), array(
                        'route' => 'littlebigjoe_frontendbundle_page_show',
                        'routeParameters' => array('slug' => $page->getSlug())
                    ));
                }
            }
        }

        return $menu;
    }
}
