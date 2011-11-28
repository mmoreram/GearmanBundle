<?php

namespace Mmoreramerino\GearmanBundle;

use Symfony\Component\Config\FileLocator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Mmoreramerino\GearmanBundle\Module\WorkerCollection;
use Mmoreramerino\GearmanBundle\Module\GearmanBaseBundle;
use Mmoreramerino\GearmanBundle\Module\WorkerDirectoryLoader;
use Mmoreramerino\GearmanBundle\Module\WorkerClass as Worker;
use Mmoreramerino\GearmanBundle\Service\GearmanCache as Cache;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Mmoreramerino\GearmanBundle\Exceptions\GearmanNotInstalledException;

/**
 * Gearman Bundle
 *
 * @author Marc Morera <marc@ulabox.com>
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
        $existsCache = $gearmanCache->existsCacheFile();

        $cacheclearEnvs = array(
            'back_dev', 'back_test', 'dev', 'test',
        );

        if (in_array($this->container->get('kernel')->getEnvironment(), $cacheclearEnvs) || !$existsCache) {

            if ($existsCache) {
                $gearmanCache->emptyCache();
            }
            $reader = new AnnotationReader();
            AnnotationRegistry::registerFile(__DIR__ . "/Driver/Gearman/GearmanAnnotations.php");
            $reader->setDefaultAnnotationNamespace('Mmoreramerino\GearmanBundle\Driver\\');
            $workerCollection = new WorkerCollection;
            $bundles = $this->container->get('kernel')->getBundles();

            foreach ($bundles as $bundle) {

                if (!\in_array($bundle->getNamespace(), $this->getParseableBundles())) {
                    continue;
                }
                $filesLoader = new WorkerDirectoryLoader(new FileLocator('.'));
                $files = $filesLoader->load($bundle->getPath());

                foreach ($files as $file) {
                    $reflClass = new \ReflectionClass($file['class']);
                    $classAnnotations = $reader->getClassAnnotations($reflClass);

                    foreach ($classAnnotations as $annot) {

                        if ($annot instanceof \Mmoreramerino\GearmanBundle\Driver\Gearman\Work) {
                            $workerCollection->add(new Worker($annot, $reflClass, $reader, $this->getSettings()));
                        }
                    }
                }
            }

            $gearmanCache   ->set($workerCollection->__toCache())
                            ->save();
        }
    }
}