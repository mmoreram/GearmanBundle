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
 * @author Marc Morera <marc@ulabox.com>
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
    private $ignored = null;

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
        $reader = new AnnotationReader();
        AnnotationRegistry::registerFile(__DIR__ . "/../Driver/Gearman/GearmanAnnotations.php");
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
                
                if ($this->isIgnore($file['class'])) {
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
     * @param string $class
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
