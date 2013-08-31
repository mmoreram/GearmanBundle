<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('gearman');

        $rootNode
            ->children()
                ->arrayNode('bundles')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('active')
                                ->defaultFalse()
                            ->end()
                            ->arrayNode('include')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('ignore')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('servers')
                    ->defaultValue(array(
                        'localhost' =>  array(
                            'hostname'  =>  '127.0.0.1',
                            'port'      =>  "4730",
                        )
                    ))
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('port')
                                ->defaultValue("4730")
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('defaults')
                    ->children()
                        ->scalarNode('iterations')
                            ->defaultValue(150)
                        ->end()
                        ->scalarNode('method')
                            ->defaultValue('do')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
