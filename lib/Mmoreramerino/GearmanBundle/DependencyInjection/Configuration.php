<?php

namespace Mmoreramerino\GearmanBundle\DependencyInjection;

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
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder
            ->root('gearman')
            ->children()
                ->arrayNode('server')
                    ->useAttributeAsKey('hostname')->prototype('scalar')->defaultValue('localhost')->end()
                    ->useAttributeAsKey('port')->prototype('scalar')->defaultValue('4730')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

}
