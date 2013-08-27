<?php

namespace Mmoreram\GearmanBundle\Module;

use Doctrine\Common\Annotations\Reader;
use Mmoreram\GearmanBundle\Driver\Gearman\Work;
use Mmoreram\GearmanBundle\Module\JobCollection;
use Mmoreram\GearmanBundle\Module\JobClass as Job;
use Mmoreram\GearmanBundle\Driver\Gearman\Job as JobAnnotation;
use ReflectionClass;
use ReflectionMethod;

/**
 * Worker class
 * 
 * This class provide all worker definition.
 * 
 * * JobCollection - All worker available jobs
 * * CallableName - Friendly way to call this worker
 * * Namespace - Worker namespace
 * * Servers - Servers defined for this worker
 *
 * @author Marc Morera <yuhu@mmoreram.com>
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
     * @var JobCollection
     * 
     * All jobs inside Worker
     */
    private $jobCollection;


    /**
     * Retrieves all jobs available from worker
     *
     * @param Work             $classAnnotation ClassAnnotation class
     * @param ReflectionClass  $reflectionClass Reflexion class
     * @param Reader           $reader          ReaderAnnotation class
     * @param array            $settings        Settings array
     */
    public function __construct(Work $classAnnotation, ReflectionClass $reflectionClass, Reader $reader, array $servers, array $defaultSettings)
    {
        $this->namespace = $reflectionClass->getNamespaceName();

        /**
         * Setting worker callable name
         */
        $this->callableName = is_null($classAnnotation->name)
                            ? $reflectionClass->getName()))
                            : $this->namespace .'\\' .$classAnnotation->name);

        $this->callableName = str_replace('\\', '', $this->callableName);

        /**
         * Setting worker description
         */
        $this->description  = is_null($classAnnotation->description)
                            ? 'No description is defined'
                            : $classAnnotation->description;

        $this->fileName = $reflectionClass->getFileName();
        $this->className = $reflectionClass->getName();
        $this->service = $classAnnotation->service;

        $this->iterations   = is_null($classAnnotation->iterations)
                            ? (int) $defaultSettings['iterations']
                            : $classAnnotation->iterations;

        $defaultSettings['iterations'] = $this->iterations;

        $this->defaultMethod    = is_null($classAnnotation->defaultMethod)
                                ? $defaultSettings['method']
                                : $classAnnotation->defaultMethod;

        $defaultSettings['method'] = $this->defaultMethod;

        /**
         * By default, this worker takes default servers definition
         */
        $this->servers = $servers;

        /**
         * If is configured some servers definition in the worker, overwrites
         */
        if ($classAnnotation->servers) {

            if (is_array($classAnnotation->servers)) {

                $this->servers = $classAnnotation->servers;
            } else {

                $this->servers = array($classAnnotation->servers);
            }
        }

        $this->jobCollection = new JobCollection;

        foreach ($reflectionClass->getMethods() as $method) {

            $reflMethod = new ReflectionMethod($method->class, $method->name);
            $methodAnnotations = $reader->getMethodAnnotations($reflMethod);

            foreach ($methodAnnotations as $annot) {

                if ($annot instanceof JobAnnotation) {

                    $job = new Job($annot, $reflMethod, $classAnnotation, $this->callableName, $this->servers, $defaultSettings);
                    $this->jobCollection->add($job);
                }
            }
        }
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
