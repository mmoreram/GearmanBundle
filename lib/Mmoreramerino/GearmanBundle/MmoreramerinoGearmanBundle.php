<?php

namespace Mmoreramerino\GearmanBundle;


use Mmoreramerino\GearmanBundle\Service\GearmanCache;
use Mmoreramerino\GearmanBundle\Module\GearmanBaseBundle;
use Mmoreramerino\GearmanBundle\Service\GearmanCacheLoader;
use Mmoreramerino\GearmanBundle\Exceptions\GearmanNotInstalledException;

/**
 * Gearman Bundle
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class MmoreramerinoGearmanBundle extends GearmanBaseBundle
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
        if (!in_array('gearman', get_loaded_extensions())) {
            throw new GearmanNotInstalledException;
        }

        $gearmanCache = $this->container->get('gearman.cache');
        $existsCache  = $gearmanCache->existsCacheFile();

        if (!$existsCache) {
            $gearmanCacheLoader = $this->container->get('gearman.cache.loader');
            $gearmanCacheLoader->load($gearmanCache);
        }
    }
}
