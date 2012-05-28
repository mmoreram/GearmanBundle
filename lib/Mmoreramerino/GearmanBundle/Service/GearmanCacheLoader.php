<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Symfony\Component\Config\FileLocator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Mmoreramerino\GearmanBundle\Service\GearmanCache;
use Mmoreramerino\GearmanBundle\Module\WorkerCollection;
use Symfony\Component\DependencyInjection\ContainerAware;
use Mmoreramerino\GearmanBundle\Module\WorkerDirectoryLoader;
use Mmoreramerino\GearmanBundle\Module\WorkerClass as Worker;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;

/**
 * Gearman cache loader class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanCacheLoader extends ContainerAware
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
    private $exclude = array();

    /**
     * This method load all data and saves all annotations into cache.
     * Also, it load all settings from Yaml file format
     *
     * @param GearmanCache $cache Cache object to perform saving
     *
     * @return boolean Return result of saving result on cache
     */
    public function load(GearmanCache $cache)
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

        $workerCollection = new WorkerCollection;
        $bundles = $this->container->get('kernel')->getBundles();
        $parseableBundles = $this->getParseableBundles();
        foreach ($bundles as $bundle) {

            $namespace = $bundle->getNamespace();
            if (!\array_key_exists($namespace, $parseableBundles)) {
                continue;
            }
            $bundleSettings = $parseableBundles[$namespace];

            $filesLoader = new WorkerDirectoryLoader(new FileLocator('.'));

            $files = $filesLoader->load($bundle->getPath());
            $includes = (isset($bundleSettings['include'])) ? (array) $bundleSettings['include'] : array();
            foreach ($files as $file) {
                if ($this->isIgnore($file['class'], $includes)) {
                    continue;
                }
                $reflClass = new \ReflectionClass($file['class']);
                $classAnnotations = $reader->getClassAnnotations($reflClass);

                foreach ($classAnnotations as $annot) {

                    if ($annot instanceof \Mmoreramerino\GearmanBundle\Driver\Gearman\Work) {
                        $workerCollection->add(new Worker($annot, $reflClass, $reader, $this->getSettings()));
                    }
                }
            }
        }

        return $cache   ->set($workerCollection->__toCache())
                        ->save();
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
                            $bundle = array();
                            $namespace = $properties['namespace'];
                            if (isset($properties['include']) && '' !== $properties['include']) {
                                foreach ((array) $properties['include'] as $include) {
                                    $bundle['include'][] =  $properties['namespace'] . '\\' . $include;
                                }

                            }
                            $this->bundles[$namespace] = $bundle;
                        }

                        if (isset($properties['exclude'])) {
                            $ignored = (array) $properties['exclude'];
                            while ($ignored) {
                                $this->exclude[] = $properties['namespace'] . '\\' . array_shift($ignored);
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
        $this->settings = $this->container->get('gearman.settings')->loadSettings();

        return $this->settings;
    }

    /**
     * Checks the class it belongs to the ignored
     *
     * @param string $class    Class name
     * @param array  $includes Includes
     *
     * @return boolean
     */
    private function isIgnore($class, $includes = array())
    {
        $found = false;
        foreach ($includes as $include) {
            if (0 === strpos($class, $include)) {
                $found = true;
                break;
            }
        }

        if ($found && null === $this->exclude) {
            return false;
        }

        if (count($includes) > 0 && !$found) {
            return true;
        }

        foreach ($this->exclude as $ns) {
            if (strstr($class, $ns) !== false) {
                return true;
            }
        }

        return false;
    }
}
