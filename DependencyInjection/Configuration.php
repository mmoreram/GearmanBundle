<?php

/**
 * Gearman Bundle for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Mmoreram\GearmanBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your
 * app/config files
 *
 * @since 2.3.1
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
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
                    ->performNoDeepMerging()
                    ->defaultValue(array(
                        'localhost' =>  array(
                            'host'  =>  '127.0.0.1',
                            'port'  =>  "4730",
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
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('iterations')
                            ->defaultValue(0)
                        ->end()
                        ->scalarNode('method')
                            ->defaultValue('doNormal')
                        ->end()
                        ->scalarNode('callbacks')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('job_prefix')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('generate_unique_key')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('workers_name_prepend_namespace')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('minimum_execution_time')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('timeout')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
