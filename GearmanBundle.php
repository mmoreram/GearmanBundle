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
        $gearmanCacheLoader = $this->container->get('gearman.cache.loader');

        if ($this->container->get('kernel')->isDebug()) {

            $gearmanCacheLoader->reloadCache($gearmanCache);

        } else {

            $gearmanCacheLoader->loadCache($gearmanCache);
        }
    }
}