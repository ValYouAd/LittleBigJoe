<?php

namespace LittleBigJoe\Bundle\MangoPayBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('little_big_joe_mango_pay');

        $rootNode
            ->children()
                ->scalarNode('leetchi_base_url')->isRequired()->cannotBeEmpty()->end()
        				->scalarNode('leetchi_private_key_file')->isRequired()->cannotBeEmpty()->end()
       			->end();
                
        return $treeBuilder;
    }
}
