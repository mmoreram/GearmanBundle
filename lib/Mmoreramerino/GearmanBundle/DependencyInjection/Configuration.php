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
        $rootNode = $treeBuilder->root('mmoreramerino_gearman');
        $rootNode->children()
            ->arrayNode('defaults')
                ->children()
                    ->scalarNode('iterations')->defaultValue(150)->info('Default number of executions before job dies. If annotations defined, will be overwritten')->end()
                    ->scalarNode('method')->defaultValue('doBackground')->end()
                    ->arrayNode('servers')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('hostname')->defaultValue('127.0.0.1')->end()
                                ->scalarNode('port')->defaultValue(4730)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('bundles')
                ->useAttributeAsKey('bundle')
                ->prototype('array')
                    ->children()
                        ->scalarNode('namespace')->isRequired()->end()
                        ->scalarNode('active')->defaultValue(true)->end()
                        ->arrayNode('ignore')
                            ->prototype('scalar')->end()
                            ->requiresAtLeastOneElement()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();


        return $treeBuilder;
    }
}
