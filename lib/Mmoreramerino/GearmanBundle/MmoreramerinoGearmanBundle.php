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

        /** @var Cache $cache  */
        $cache = $this->container->get(self::CACHE_SERVICE);
        $existsCache = $cache->contains(self::CACHE_ID);

        $cacheclearEnvs = array(
            'back_dev', 'back_test', 'dev', 'test',
        );

        if (in_array($this->container->get('kernel')->getEnvironment(), $cacheclearEnvs) || !$existsCache) {
            if ($existsCache) {
                $cache->delete(self::CACHE_ID);
            }

            /** @var GearmanLoader $gearmanCacheLoader  */
            $gearmanCacheLoader = $this->container->get('gearman.loader');
            $gearmanCacheLoader->load($cache);
        }
    }
}