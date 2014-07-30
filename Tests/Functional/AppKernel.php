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

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * AppKernel for testing
 */
class AppKernel extends Kernel
{
    /**
     * Only register GearmanBundle
     */
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Mmoreram\GearmanBundle\GearmanBundle(),
        );
    }

    /**
     * Setup configuration file.
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(dirname(__FILE__) . '/config.yml');
    }
}
