<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Symfony\Component\Config\FileLocator;
use Mmoreramerino\GearmanBundle\MmoreramerinoGearmanBundle;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\DependencyInjection\ContainerAware;
use Mmoreramerino\GearmanBundle\Module\WorkerDirectoryLoader;
use Mmoreramerino\GearmanBundle\Module\WorkerClass as Worker;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Gearman loader class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanLoader extends ContainerAware
{


    /**
     * Settings defined into settings file
     *
     * @var Array
     */
    private $settings = null;


    /**
     * Bundles available to perform search setted in bundles.yml file
     *
     * @var Array
     */
    private $bundles = null;

    /**
     * Ignored namespaces
     *
     * @var Array
     */
    private $ignored = null;

    /**
     * This method load all data and saves all annotations into cache.
     * Also, it load all settings from Yaml file format
     *
     * @param Cache $cache Cache object to perform saving
     *
     * @return boolean Return result of saving result on cache
     */
    public function load(Cache $cache)
    {
        if (version_compare(\Doctrine\Common\Version::VERSION, '2.2.0-DEV', '>=')) {
            // Register the ORM Annotations in the AnnotationRegistry
            AnnotationRegistry::registerFile(__DIR__ . "/../Driver/Gearman/Work.php");
            AnnotationRegistry::registerFile(__DIR__ . "/../Driver/Gearman/Job.php");

            $reader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
            $reader->addNamespace('Mmoreramerino\GearmanBundle\Driver');
        } else {
            // Register the ORM Annotations in the AnnotationRegistry
            AnnotationRegistry::registerFile(__DIR__ . "/../Driver/Gearman/Work.php");
            AnnotationRegistry::registerFile(__DIR__ . "/../Driver/Gearman/Job.php");

            $reader = new AnnotationReader();
            $reader->setDefaultAnnotationNamespace('Mmoreramerino\GearmanBundle\Driver\\');
        }

        $workerCollection = array();
        /** @var BundleInterface[] $bundles */
        $bundles = $this->container->get('kernel')->getBundles();
        foreach ($bundles as $bundle) {
            if (!\in_array($bundle->getNamespace(), $this->getParseableBundles())) {
                continue;
            }
            $filesLoader = new WorkerDirectoryLoader(new FileLocator('.'));
            $files = $filesLoader->load($bundle->getPath());

            foreach ($files as $file) {

                if ($this->isIgnore($file['class'])) {
                    continue;
                }

                $reflClass = new \ReflectionClass($file['class']);
                $classAnnotations = $reader->getClassAnnotations($reflClass);

                foreach ($classAnnotations as $annot) {
                    if ($annot instanceof \Mmoreramerino\GearmanBundle\Driver\Gearman\Work) {
                        $worker = new Worker();
                        $workerCollection[] = $worker;
                        $worker->init($annot, $reflClass, $reader, $this->getSettings());
                    }
                }
            }
        }

        return $cache->save(MmoreramerinoGearmanBundle::CACHE_ID, $workerCollection);
    }

    /**
     * Return Gearman bundle settings, previously loaded by method load()
     * If settings are not loaded, a SettingsNotLoadedException Exception is thrown
     *
     * @return array Bundles that gearman will be able to search annotations
     */
    public function getParseableBundles()
    {
        if (null === $this->settings) {
            $this->loadSettings();
        }

        if (null === $this->bundles) {
            $this->bundles = array();

            if (isset($this->settings['bundles']) && is_array($this->settings['bundles']) && !empty($this->settings['bundles'])) {

                foreach ($this->settings['bundles'] as $properties) {

                    if ( isset($properties['active']) && (true === $properties['active']) ) {

                        if ('' !== $properties['namespace']) {
                            $this->bundles[] = $properties['namespace'];
                        }

                        if (isset($properties['ignore'])) {
                            $ignored = (array) $properties['ignore'];
                            while ($ignored) {
                                $this->ignored[] = $properties['namespace'] . '\\' . array_shift($ignored);
                            }
                        }
                    }
                }
            }
        }

        return $this->bundles;
    }


    /**
     * Return Gearman settings
     *
     * @return array Settings getted from gearmanSettings service
     */
    public function getSettings()
    {
        return $this->loadSettings();
    }

    /**
     * Get yaml file and load all settings for Gearman engine
     *
     * @return array Settings
     */
    public function loadSettings()
    {
        $this->settings = $this->container->getParameter('gearman_settings');

        return $this->settings;
    }

    /**
     * Checks the class it belongs to the ignored
     *
     * @param string $class Class name
     *
     * @return boolean
     */
    public function isIgnore($class)
    {
        if (null === $this->ignored) {
            return false;
        }

        foreach ($this->ignored as $ns) {
            if (strstr($class, $ns) !== false) {
                return true;
            }
        }

        return false;
    }
}
