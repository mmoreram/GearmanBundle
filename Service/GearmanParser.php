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
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;

use Mmoreram\GearmanBundle\Module\WorkerCollection;
use Mmoreram\GearmanBundle\Module\WorkerClass as Worker;
use Mmoreram\GearmanBundle\Driver\Gearman\Work as WorkAnnotation;

/**
 * Gearman parsing methods
 * 
 * This class has responability of parsing, if needed, all defined bundle files
 * looking for some Workers.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanParser
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
     * @var Reader
     *
     * Annotation Reader
     */
    private $reader;


    /**
     * @var Finder
     *
     * Finder
     */
    private $finder;


    /**
     * @var Array
     *
     * Bundles available to perform search
     */
    private $bundles;


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
     * Construct method
     *
     * @param KernelInterface $kernel          Kernel instance
     * @param Reader          $reader          Reader
     * @param Finder          $finder          Finder
     * @param array           $bundles         Bundle array where to parse workers, defined on condiguration
     * @param array           $servers         Server list defined on configuration
     * @param array           $defaultSettings Default settings defined on configuration
     */
    public function __construct(KernelInterface $kernel, Reader $reader, Finder $finder, array $bundles, array $servers, array $defaultSettings)
    {
        $this->kernelBundles = $kernel->getBundles();
        $this->kernel = $kernel;
        $this->reader = $reader;
        $this->finder = $finder;
        $this->bundles = $bundles;
        $this->servers = $servers;
        $this->defaultSettings = $defaultSettings;
    }


    /**
     * Loads Worker Collection from parsed files
     * 
     * @return WorkerCollection collection of all info
     */
    public function load()
    {
        list($paths, $excludedPaths) = $this->loadNamespaceMap($this->kernelBundles, $this->bundles);

        return $this->parseNamespaceMap($this->finder, $this->reader, $paths, $excludedPaths);
    }


    /**
     * Return Gearman bundle settings, previously loaded by method load()
     *
     * If settings are not loaded, a SettingsNotLoadedException Exception is thrown
     * 
     * @param array $kernelBundles Kernel bundles
     * @param array $bundles       Bundle array of settings
     * 
     * @return array Return an array containing paths and ignore paths
     */
    public function loadNamespaceMap(array $kernelBundles, array $bundles)
    {
        $paths = array();
        $excludedPaths = array();

        /**
         * Iteratinc all bundle settings
         */
        foreach ($bundles as $bundleSettings) {

            if (!$bundleSettings['active']) {

                break;
            }

            $bundleNamespace = $bundleSettings['name'];
            $bundlePath = $kernelBundles[$bundleNamespace]->getPath();

            if (!empty($bundleSettings['include'])) {

                foreach ($bundleSettings['include'] as $include) {

                    $paths[] = rtrim(rtrim($bundlePath, '/') . '/' . $include, '/') . '/';
                }

            } else {

                /**
                 * If no include is set, include all namespace
                 */
                $paths[] = rtrim($bundlePath, '/') . '/';
            }

            foreach ($bundleSettings['ignore'] as $ignore) {

                $excludedPaths[] = trim($ignore, '/');
            }
        }

        return array(
            $paths,
            $excludedPaths,
        );
    }


    /**
     * Perform a parsing inside all namespace map
     * 
     * Creates an empty worker collection and, if exist some parseable files
     * parse them, filling this object
     * 
     * @param Finder $finder        Finder
     * @param Reader $reader        Reader
     * @param array  $paths         Paths where to look for
     * @param array  $excludedPaths Paths to ignore
     *
     * @return WorkerCollection collection of all info
     */
    public function parseNamespaceMap(Finder $finder, Reader $reader, array $paths, array $excludedPaths)
    {
        $workerCollection = new WorkerCollection;

        if (!empty($paths)) {

            $finder
                ->files()
                ->followLinks()
                ->exclude($excludedPaths)
                ->in($paths)
                ->name('*.php');

            $this->parseFiles($finder, $reader, $workerCollection);
        }

        return $workerCollection;
    }


    /**
     * Load all workers with their jobs
     *
     * @param Finder           $finder           Finder
     * @param Reader           $reader           Reader
     * @param WorkerCollection $workerCollection Worker collection
     *
     * @return GearmanParser self Object
     */
    public function parseFiles(Finder $finder, Reader $reader, WorkerCollection $workerCollection)
    {

        /**
         * Every file found is parsed
         */
        foreach ($finder as $file) {

            /**
             * File is accepted to be parsed
             */
            $classNamespace = $this->getFileClassNamespace($file->getRealpath());
            $reflectionClass = new ReflectionClass($classNamespace);
            $classAnnotations = $reader->getClassAnnotations($reflectionClass);

            /**
             * Every annotation found is parsed
             */
            foreach ($classAnnotations as $annotation) {

                /**
                 * Annotation is only laoded if is typeof WorkAnnotation
                 */
                if ($annotation instanceof WorkAnnotation) {

                    /**
                     * Creates new Worker element with all its Job data
                     */
                    $worker = new Worker($annotation, $reflectionClass, $reader, $this->servers, $this->defaultSettings);
                    $workerCollection->add($worker);
                }
            }
        }

        return $this;
    }


    /**
     * Returns file class namespace, if exists
     *
     * @param string $file A PHP file path
     *
     * @return string|false Full class namespace if found, false otherwise
     */
    public function getFileClassNamespace($file)
    {
        $filenameBlock = explode(DIRECTORY_SEPARATOR, $file);
        $filename = explode('.', end($filenameBlock), 2);
        $filename = reset($filename);

        preg_match('/\snamespace\s+(.+?);/s', file_get_contents($file), $match);

        return    is_array($match) && isset($match[1])
                ? $match[1] . '\\' . $filename
                : false;
    }
}
