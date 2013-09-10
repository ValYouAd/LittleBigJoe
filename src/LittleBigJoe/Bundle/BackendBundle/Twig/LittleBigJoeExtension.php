<?php

namespace LittleBigJoe\Bundle\BackendBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig controller.
 *
 */
class LittleBigJoeExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            'current' => new \Twig_Filter_Method($this, 'isCurrent'),
        );
    }

    public function isCurrent($routePrefix)
    {
        $request = $this->container->get('request');
        $routeName = $request->get('_route');

        return preg_match('#^' . $routePrefix . '#', $routeName) ? 'active' : '';
    }

    public function getName()
    {
        return 'littlebigjoebackend_extension';
    }
} 