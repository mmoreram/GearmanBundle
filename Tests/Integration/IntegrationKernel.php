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

namespace Mmoreram\GearmanBundle\Tests\Integration;

use Mmoreram\GearmanBundle\GearmanBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Kernel for the integration tests. Workers are loaded from Fixtures/Worker,
 * relative to this file, and jobs are sent to the gearmand server configured
 * in config.yml.
 */
class IntegrationKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new GearmanBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config.yml');
    }

    public function getCacheDir(): string
    {
        return $this->getBaseDir() . '/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getBaseDir() . '/logs';
    }

    /**
     * GearmanRoundTripTest sets GEARMAN_INTEGRATION_CACHE_DIR to a fresh
     * directory per run, shared with the worker processes, so a worker
     * (booted without debug, hence without container freshness checks) never
     * reuses a container compiled from an older configuration.
     */
    private function getBaseDir(): string
    {
        return getenv('GEARMAN_INTEGRATION_CACHE_DIR') ?: sys_get_temp_dir() . '/gearman-bundle-integration';
    }
}
