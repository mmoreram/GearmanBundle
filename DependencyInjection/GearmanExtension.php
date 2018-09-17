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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @since 2.3.1
 */
class GearmanExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);

        /**
         * Setting all config elements as DI parameters to inject them
         */
        $container->setParameter(
            'gearman.bundles',
            $config['bundles']
        );

        $container->setParameter(
            'gearman.resources',
            $config['resources']
        );

        $container->setParameter(
            'gearman.servers',
            $config['servers']
        );

        $container->setParameter(
            'gearman.default.settings',
            $config['defaults']
        );

        $container->setParameter(
            'gearman.default.settings.generate_unique_key',
            $config['defaults']['generate_unique_key']
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        /**
         * Loading DI definitions
         */
        $loader->load('classes.yml');
        $loader->load('services.yml');
        $loader->load('commands.yml');
        $loader->load('eventDispatchers.yml');
        $loader->load('generators.yml');
        $loader->load('externals.yml');
    }
}
