<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Module;

use Doctrine\Common\Annotations\Reader;
use Mmoreram\GearmanBundle\Module\JobCollection;
use Mmoreram\GearmanBundle\Module\JobClass as Job;
use Mmoreram\GearmanBundle\Driver\Gearman\Job as JobAnnotation;
use Mmoreram\GearmanBundle\Driver\Gearman\Work as WorkAnnotation;
use ReflectionClass;
use ReflectionMethod;

/**
 * Worker class
 * 
 * This class provide all worker definition.
 */
class WorkerClass
{

    /**
     * @var string
     * 
     * Namespace of worker class
     */
    private $namespace;


    /**
     * @var string
     * 
     * Class name of worker
     */
    private $className;


    /**
     * @var string
     * 
     * Filename of worker
     */
    private $fileName;


    /**
     * @var string
     * 
     * Callable name for this job.
     * If is setted on annotations, this value will be used.
     * Otherwise, natural method name will be used.
     */
    private $callableName;


    /**
     * @var string
     * 
     * Service alias if this worker is wanted to be built by dependency injection
     */
    private $service;


    /**
     * @var string
     * 
     * Description of Job
     */
    private $description;


    /**
     * @var integer
     * 
     * Number of iterations this job will be alive before die
     */
    private $iterations;


    /**
     * @var string
     * 
     * Default method this job will be call into Gearman client
     */
    private $defaultMethod;


    /**
     * @var array
     * 
     * Collection of servers to connect
     */
    private $servers;


    /**
     * @var array
     * 
     * settings
     */
    private $settings;


    /**
     * @var JobCollection
     * 
     * All jobs inside Worker
     */
    private $jobCollection;


    /**
     * Retrieves all jobs available from worker
     *
     * @param WorkAnnotation  $workAnnotation  workAnnotation class
     * @param ReflectionClass $reflectionClass Reflexion class
     * @param Reader          $reader          ReaderAnnotation class
     * @param array           $servers         Array of servers defined for Worker
     * @param array           $defaultSettings Default settings for Worker
     */
    public function __construct(WorkAnnotation $workAnnotation, ReflectionClass $reflectionClass, Reader $reader, array $servers, array $defaultSettings)
    {
        $this->namespace = $reflectionClass->getNamespaceName();

        /**
         * Setting worker callable name
         */
        $this->callableName = is_null($workAnnotation->name)
                            ? $reflectionClass->getName()
                            : $this->namespace .'\\' .$workAnnotation->name;

        $this->callableName = str_replace('\\', '', $this->callableName);

        /**
         * Setting worker description
         */
        $this->description  = is_null($workAnnotation->description)
                            ? 'No description is defined'
                            : $workAnnotation->description;

        $this->fileName = $reflectionClass->getFileName();
        $this->className = $reflectionClass->getName();
        $this->service = $workAnnotation->service;

        
        $this
            ->loadSettings($workAnnotation, $defaultSettings)
            ->loadServers($workAnnotation, $servers)
            ->createJobCollection($reflectionClass, $reader);
    }


    /**
     * Load settings
     * 
     * @param WorkAnnotation $workAnnotation WorkAnnotation class
     * @param array          $servers        Array of servers defined for Worker
     * 
     * @return WorkerClass self Object
     */
    private function loadServers(WorkAnnotation $workAnnotation, array $servers)
    {
        /**
         * By default, this worker takes default servers definition
         */
        $this->servers = $servers;

        /**
         * If is configured some servers definition in the worker, overwrites
         */
        if ($workAnnotation->servers) {

            $this->servers  = ( is_array($workAnnotation->servers) && !isset($workAnnotation->servers['host']) )
                            ? $workAnnotation->servers
                            : array($workAnnotation->servers);
        }

        return $this;
    }


    /**
     * Load settings
     * 
     * @param WorkAnnotation $workAnnotation  WorkAnnotation class
     * @param array          $defaultSettings Default settings for Worker
     * 
     * @return WorkerClass self Object
     */
    private function loadSettings(WorkAnnotation $workAnnotation, array $defaultSettings)
    {
        $this->iterations   = is_null($workAnnotation->iterations)
                            ? (int) $defaultSettings['iterations']
                            : $workAnnotation->iterations;

        $defaultSettings['iterations'] = $this->iterations;

        $this->defaultMethod    = is_null($workAnnotation->defaultMethod)
                                ? $defaultSettings['method']
                                : $workAnnotation->defaultMethod;

        $defaultSettings['method'] = $this->defaultMethod;

        $this->settings = $defaultSettings;

        return $this;
    }


    /**
     * Creates job collection of worker
     * 
     * @param ReflectionClass $reflectionClass Reflexion class
     * @param Reader          $reader          ReaderAnnotation class
     * 
     * @return WorkerClass self Object
     */
    private function createJobCollection(ReflectionClass $reflectionClass, Reader $reader)
    {
        $this->jobCollection = new JobCollection;

        /**
         * For each defined method, we parse it
         */
        foreach ($reflectionClass->getMethods() as $method) {

            $reflectionMethod = new ReflectionMethod($method->class, $method->name);
            $methodAnnotations = $reader->getMethodAnnotations($reflectionMethod);

            /**
             * Every annotation found is parsed
             */
            foreach ($methodAnnotations as $methodAnnotation) {

                /**
                 * Annotation is only laoded if is typeof JobAnnotation
                 */
                if ($methodAnnotation instanceof JobAnnotation) {

                    /**
                     * Creates new Job
                     */
                    $job = new Job($methodAnnotation, $reflectionMethod, $this->callableName, $this->servers, $this->settings);
                    $this->jobCollection->add($job);
                }
            }
        }

        return $this;
    }


    /**
     * Retrieve all Worker data in cache format
     *
     * @return array
     */
    public function toArray()
    {
        return array(

            'namespace'     =>  $this->namespace,
            'className'     =>  $this->className,
            'fileName'      =>  $this->fileName,
            'callableName'  =>  $this->callableName,
            'description'   =>  $this->description,
            'service'       =>  $this->service,
            'servers'       =>  $this->servers,
            'iterations'    =>  $this->iterations,
            'jobs'          =>  $this->jobCollection->toArray(),
        );
    }

}
