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

namespace Mmoreram\GearmanBundle\Tests\Functional;

use Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle;
use Mmoreram\GearmanBundle\GearmanBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
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
            new FrameworkBundle(),
            new DoctrineCacheBundle(),
            new GearmanBundle(),
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
