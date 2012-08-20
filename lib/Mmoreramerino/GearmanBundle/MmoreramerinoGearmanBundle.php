<?php

namespace Mmoreramerino\GearmanBundle;


use Mmoreramerino\GearmanBundle\Service\GearmanLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mmoreramerino\GearmanBundle\Exceptions\GearmanNotInstalledException;
use Doctrine\Common\Cache\Cache;

/**
 * Gearman Bundle
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class MmoreramerinoGearmanBundle extends Bundle
{
    const CACHE_SERVICE = 'liip_doctrine_cache.ns.mmoreramerino_gearman';
    const CACHE_ID = 'workers';

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
    }
}