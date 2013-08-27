<?php

namespace Mmoreram\GearmanBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Gearman Bundle
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanBundle extends Bundle
{
    /**
     * Boots the Bundle.
     * This method load all data and saves all annotations into cache.
     * Also, it load all settings from Yaml file format
     *
     * @api
     */
    public function boot()
    {
        $gearmanCache = $this->container->get('gearman.cache');
        $existsCache = $gearmanCache->existsCacheFile();

        if ($this->container->get('kernel')->isDebug() || !$existsCache) {

            if ($existsCache) {

                $gearmanCache->emptyCache();
            }

            $gearmanCacheLoader = $this->container->get('gearman.cache.loader');
            $gearmanCacheLoader->load($gearmanCache);
        }
    }


    /**
     * Shutdowns the Bundle.
     *
     * @api
     */
    public function shutdown()
    {

    }

    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @api
     */
    public function build(ContainerBuilder $container)
    {

    }
}