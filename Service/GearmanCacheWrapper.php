<?php

namespace Mmoreram\GearmanBundle\Service;

use Symfony\Component\Config\FileLocator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Doctrine\Common\Cache\Cache;

use Mmoreram\GearmanBundle\Module\WorkerCollection;
use Mmoreram\GearmanBundle\Module\WorkerDirectoryLoader;
use Mmoreram\GearmanBundle\Module\WorkerClass as Worker;
use Mmoreram\GearmanBundle\Driver\Gearman\Work;
use ReflectionClass;

/**
 * Gearman cache loader class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanCacheWrapper
{

    /**
     * Bundles loaded by kernel
     *
     * @var Array
     */
    private $kernelBundles;


    /**
     * Bundles available to perform search
     *
     * @var Array
     */
    private $bundles;


    /**
     * @var Array
     *
     * bundles to parse on
     */
    private $bundlesAccepted = array();


    /**
     * @var Array
     *
     * accepted namespaces
     */
    private $namespacesAccepted = array();


    /**
     * @var Array
     *
     * Ignored namespaces
     */
    private $namespacesIgnored = array();


    /**
     * @var GearmanCache
     *
     * Gearman Cache
     */
    private $cache;


    /**
     * @var string
     * 
     * Gearman cache id
     */
    private $cacheId;


    /**
     * @var array
     * 
     * WorkerCollection with all workers and jobs available
     */
    private $workerCollection;


    /**
     * Return workerCollection
     * 
     * @return array all available workers
     */
    public function getWorkers()
    {
        return $workerCollection;
    }


    /**
     * Construct method
     *
     * @param array $bundles Bundles
     */
    public function __construct(array $bundles, Kernel $kernel, Cache $cache, $cacheId)
    {
        $this->kernelBundles = $kernel->getBundles();
        $this->bundles = $bundles;
        $this->cache = $cache;
        $this->cacheId = $cacheId;
    }


    /**
     * loads Gearman cache, only if is not loaded yet
     *
     * @return GearmanCacheLoader self Object
     */
    public function loadCache()
    {
        if (!$this->cache->contains($this->cacheId)) {

            $this->workerCollection = $this->parseNamespaceMap()->toArray();
            $this->cache->save($this->cacheId, $workerCollection);
        }

        return $this;
    }


    /**
     * Reloads Gearman cache
     *
     * @return GearmanCacheLoader self Object
     */
    public function reloadCache()
    {
        $this->cache->delete($this->cacheId);

        return $this->loadCache();
    }


    /**
     * Perform a parsing inside all namespace map
     *
     * @return WorkerCollection collection of all info
     */
    private function parseNamespaceMap()
    {
        AnnotationRegistry::registerFile(__DIR__ . "/../Driver/Gearman/Work.php");
        AnnotationRegistry::registerFile(__DIR__ . "/../Driver/Gearman/Job.php");

        /**
         * Depending on Symfony2 version
         */
        if (version_compare(\Doctrine\Common\Version::VERSION, '2.2.0-DEV', '>=')) {

            $reader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
            $reader->addNamespace('Mmoreram\GearmanBundle\Driver');
        } else {

            $reader = new AnnotationReader();
            $reader->setDefaultAnnotationNamespace('Mmoreram\GearmanBundle\Driver\\');
        }

        $workerCollection = new WorkerCollection;

        foreach ($this->kernelBundles as $kernelBundle) {

            if (!in_array($kernelBundle->getNamespace(), $this->bundlesAccepted)) {

                continue;
            }

            $filesLoader = new WorkerDirectoryLoader(new FileLocator('.'));
            $files = $filesLoader->load($bundle->getPath());

            foreach ($files as $file) {

                foreach ($this->namespacesIgnored as $namespaceIgnored) {

                    if ($this->isSubNamespace($namespaceIgnored, $file['class'])) {

                        continue 2;
                    }
                }

                foreach ($this->namespacesAccepted as $namespaceAccepted) {

                    if ($this->isSubNamespace($namespaceAccepted, $file['class'])) {

                        /**
                         * File is accepted to be parsed
                         */
                        $reflClass = new ReflectionClass($file['class']);
                        $classAnnotations = $reader->getClassAnnotations($reflClass);

                        foreach ($classAnnotations as $annot) {

                            if ($annot instanceof Work) {

                                $workerCollection->add(new Worker($annot, $reflClass, $reader, $this->getSettings()));
                            }
                        }

                        continue 2;
                    }
                }
            }
        }

        return $workerCollection;
    }


    /**
     * Return Gearman bundle settings, previously loaded by method load()
     * If settings are not loaded, a SettingsNotLoadedException Exception is thrown
     *
     * @return array Bundles that gearman will be able to search annotations
     */
    private function loadNamespacesMap()
    {

        foreach ($this->bundles as $bundleSettings) {

            $bundleNamespace = $properties['namespace'];

            if ($bundleSettings['active'])) {

                $this->bundlesAccepted[] = $bundleNamespace;

                if (!empty($properties['include'])) {

                    foreach ($properties['include'] as $include) {

                        $this->namespaceAccepted[] = $bundleNamespace . '\\' . $include;
                    }

                } else {

                    /**
                     * If no include is set, include all namespace
                     */
                    $this->namespaceAccepted[] = $bundleNamespace;

                }

                foreach ($properties['ignore'] as $ignore) {

                    $this->namespacesIgnored[] = $bundleNamespace . '\\' . $ignore;
                }
            }
        }
    }


    /**
     * Checks if namespace is subnamespace of another
     *
     * @param string $namespace    Parent namespace
     * @param string $subNamespace Namespace to check
     *
     * @return boolean
     */
    private function isSubNamespace($namespace, $subNamespace)
    {
        return ( strstr($class, $ns) !== false );
    }
}
