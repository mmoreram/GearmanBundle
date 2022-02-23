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
        $treeBuilder = new TreeBuilder('gearman');

        // Keep compatibility with symfony/config < 4.2
        if (!method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->root('gearman');
        } else {
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->children()
                ->arrayNode('bundles')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->booleanNode('active')
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
                ->arrayNode('resources')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('servers')
                    ->performNoDeepMerging()
                    ->defaultValue([
                        'localhost' =>  [
                            'host'  =>  '127.0.0.1',
                            'port'  =>  '4730',
                        ],
                    ])
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->integerNode('port')
                                ->defaultValue('4730')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('iterations')
                            ->min(0)
                            ->defaultValue(0)
                        ->end()
                        ->scalarNode('method')
                            ->defaultValue('doNormal')
                        ->end()
                        ->booleanNode('callbacks')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('job_prefix')
                            ->defaultNull()
                        ->end()
                        ->booleanNode('generate_unique_key')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('workers_name_prepend_namespace')
                            ->defaultTrue()
                        ->end()
                        ->integerNode('minimum_execution_time')
                            ->min(0)
                            ->defaultValue(0)
                        ->end()
                        ->integerNode('timeout')
                            ->min(0)
                            ->defaultValue(0)
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
