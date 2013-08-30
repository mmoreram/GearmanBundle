<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

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
     * @var array
     * 
     * Collection of servers to connect
     */
    private $servers;


    /**
     * @var array
     * 
     * Default settings defined by user in config.yml
     */
    private $defaultSettings;


    /**
     * Return workerCollection
     * 
     * @return array all available workers
     */
    public function getWorkers()
    {
        return $this->workerCollection;
    }


    /**
     * Construct method
     *
     * @param array $bundles Bundles
     */
    public function __construct(Kernel $kernel, Cache $cache, $cacheId, array $bundles, array $servers, array $defaultSettings)
    {
        $this->kernelBundles = $kernel->getBundles();
        $this->bundles = $bundles;
        $this->cache = $cache;
        $this->cacheId = $cacheId;
        $this->servers = $servers;
        $this->defaultSettings = $defaultSettings;
    }


    /**
     * loads Gearman cache, only if is not loaded yet
     *
     * @return GearmanCacheLoader self Object
     */
    public function load()
    {
        if ($this->cache->contains($this->cacheId)) {

            $this->workerCollection = $this->cache->get($this->cacheId);

        } else {

            $this->workerCollection = $this->parseNamespaceMap()->toArray();
            $this->cache->save($this->cacheId, $this->workerCollection);
        }

        return $this;
    }


    /**
     * flush all cache
     * 
     * @return GearmanCacheLoader self Object
     */
    public function flush()
    {
        $this->cache->delete($this->cacheId);

        return $this;
    }


    /**
     * Return Gearman bundle settings, previously loaded by method load()
     * If settings are not loaded, a SettingsNotLoadedException Exception is thrown
     *
     * @return array Bundles that gearman will be able to search annotations
     */
    public function loadNamespaceMap()
    {
        foreach ($this->bundles as $bundleSettings) {

            $bundleNamespace = $bundleSettings['namespace'];

            if ($bundleSettings['active']) {

                $this->bundlesAccepted[] = $bundleNamespace;

                if (!empty($bundleSettings['include'])) {

                    foreach ($bundleSettings['include'] as $include) {

                        $this->namespacesAccepted[] = $bundleNamespace . '\\' . $include;
                    }

                } else {

                    /**
                     * If no include is set, include all namespace
                     */
                    $this->namespacesAccepted[] = $bundleNamespace;

                }

                foreach ($bundleSettings['ignore'] as $ignore) {

                    $this->namespacesIgnored[] = $bundleNamespace . '\\' . $ignore;
                }
            }
        }
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
            $files = $filesLoader->load($kernelBundle->getPath());

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

                                $worker = new Worker($annot, $reflClass, $reader, $this->servers, $this->defaultSettings);
                                $workerCollection->add($worker);
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
     * Checks if namespace is subnamespace of another
     *
     * @param string $namespace    Parent namespace
     * @param string $subNamespace Namespace to check
     *
     * @return boolean
     */
    private function isSubNamespace($namespace, $subNamespace)
    {
        return ( strpos($subNamespace, $namespace) === 0 );
    }
}
