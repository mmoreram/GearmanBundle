<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use ReflectionClass;

use Mmoreram\GearmanBundle\Module\WorkerCollection;
use Mmoreram\GearmanBundle\Module\WorkerClass as Worker;
use Mmoreram\GearmanBundle\Driver\Gearman\Work as WorkAnnotation;

/**
 * Gearman cache loader class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanCacheWrapper implements CacheClearerInterface, CacheWarmerInterface
{

    /**
     * @var Array
     *
     * Bundles loaded by kernel
     */
    private $kernelBundles;


    /**
     * @var Kernel
     *
     * Kernel object
     */
    private $kernel;


    /**
     * @var Array
     *
     * Bundles available to perform search
     */
    private $bundles;


    /**
     * @var Array
     *
     * Paths to search on
     */
    private $paths = array();


    /**
     * @var Array
     *
     * Paths to ignore
     */
    private $excludedPaths = array();


    /**
     * @var Cache
     *
     * Cache instance
     */
    private $cache;


    /**
     * @var string
     *
     * Cache id
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
     * @param KernelInterface $kernel          Kernel instance
     * @param Cache           $cache           Cache instance
     * @param string          $cacheId         Cache id
     * @param array           $bundles         Bundle array where to parse workers, defined on condiguration
     * @param array           $servers         Server list defined on configuration
     * @param array           $defaultSettings Default settings defined on configuration
     */
    public function __construct(KernelInterface $kernel, Cache $cache, $cacheId, array $bundles, array $servers, array $defaultSettings)
    {
        $this->kernelBundles = $kernel->getBundles();
        $this->kernel = $kernel;
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

            $this->workerCollection = $this->cache->fetch($this->cacheId);

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
     *
     * If settings are not loaded, a SettingsNotLoadedException Exception is thrown
     */
    public function loadNamespaceMap()
    {
        /**
         * Iteratinc all bundle settings
         */
        foreach ($this->bundles as $bundleSettings) {

            if (!$bundleSettings['active']) {

                break;
            }

            $bundleNamespace = $bundleSettings['name'];
            $bundlePath = $this->kernelBundles[$bundleNamespace]->getPath();

            if (!empty($bundleSettings['include'])) {

                foreach ($bundleSettings['include'] as $include) {

                    $this->paths[] = rtrim(rtrim($bundlePath, '/') . '/' . $include, '/') . '/';
                }

            } else {

                /**
                 * If no include is set, include all namespace
                 */
                $this->paths[] = rtrim($bundlePath, '/') . '/';
            }

            foreach ($bundleSettings['ignore'] as $ignore) {

                $this->excludedPaths[] = trim($ignore, '/');
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
        AnnotationRegistry::registerFile($this->kernel->locateResource("@GearmanBundle/Driver/Gearman/Work.php"));
        AnnotationRegistry::registerFile($this->kernel->locateResource("@GearmanBundle/Driver/Gearman/Job.php"));

        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('Mmoreram\GearmanBundle\Driver');
        $workerCollection = new WorkerCollection;

        if (!empty($this->paths)) {

            $finder = new Finder();
            $finder
                ->files()
                ->followLinks()
                ->exclude($this->excludedPaths)
                ->in($this->paths)
                ->name('*.php');

            $workerCollection = $this->parseFiles($finder, $reader);
        }

        return $workerCollection;
    }


    /**
     * Load all workers with their jobs
     *
     * @param Finder $finder Finder
     * @param Reader $reader Reader
     *
     * @return WorkerCollection collection of all info
     */
    private function parseFiles(Finder $finder, Reader $reader)
    {

        $workerCollection = new WorkerCollection;

        /**
         * Every file found is parsed
         */
        foreach ($finder as $file) {

            /**
             * File is accepted to be parsed
             */
            $classNamespace = $this->getFileClassNamespace($file->getRealpath());
            $reflClass = new ReflectionClass($classNamespace);
            $classAnnotations = $reader->getClassAnnotations($reflClass);

            /**
             * Every annotation found is parsed
             */
            foreach ($classAnnotations as $annot) {

                /**
                 * Annotation is only laoded if is typeof WorkAnnotation
                 */
                if ($annot instanceof WorkAnnotation) {

                    /**
                     * Creates new Worker element with all its Job data
                     */
                    $worker = new Worker($annot, $reflClass, $reader, $this->servers, $this->defaultSettings);
                    $workerCollection->add($worker);
                }
            }
        }

        return $workerCollection;
    }


    /**
     * Cache clear implementation
     *
     * @param string $cacheDir The cache directory
     */
    public function clear($cacheDir)
    {
        $this->flush();
    }


    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        $this->load();
    }


    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * As GearmanBundle loads cache incrementaly so is optional
     *
     * @return Boolean true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return true;
    }


    /**
     * Returns file class namespace
     *
     * @param string $file A PHP file path
     *
     * @return string|false Full class namespace if found, false otherwise
     */
    protected function getFileClassNamespace($file)
    {
        $filenameBlock = explode('/', $file);
        $filename = explode('.', end($filenameBlock), 2);
        $filename = reset($filename);

        preg_match('/\snamespace\s+(.+?);/s', file_get_contents($file), $match);

        return    is_array($match) && isset($match[1])
                ? $match[1] . '\\' . $filename
                : false;
    }
}
