<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Module;

use Mmoreram\GearmanBundle\Driver\Gearman\Job as JobAnnotation;
use Mmoreram\GearmanBundle\Driver\Gearman\Work as WorkAnnotation;
use Symfony\Component\DependencyInjection\ContainerAware;
use ReflectionMethod;

/**
 * Job class
 * 
 * This class provide all worker definition.
 */
class JobClass extends ContainerAware
{

    /**
     * @var string
     * 
     * Callable name for this job
     * If is setted on annotations, this value will be used
     *  otherwise, natural method name will be used.
     */
    private $callableName;


    /**
     * @var string
     * 
     * Method name
     */
    private $methodName;


    /**
     * @var string
     * 
     * RealCallable name for this job
     * natural method name will be used.
     */
    private $realCallableName;


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
     * Construct method
     *
     * @param JobAnnotation    $jobAnnotation  jobAnnotation class
     * @param ReflectionMethod $method            ReflextionMethod class
     * @param string           $callableNameClass Callable name class
     * @param array            $servers           Array of servers defined for Worker
     * @param array            $defaultSettings   Default settings for Worker
     */
    public function __construct(JobAnnotation $jobAnnotation, ReflectionMethod $method, $callableNameClass, array $servers, array $defaultSettings)
    {
        $this->callableName = is_null($jobAnnotation->name)
                            ? $method->getName()
                            : $jobAnnotation->name;

        $this->methodName = $method->getName();

        $this->realCallableName = str_replace('\\', '', $callableNameClass . '~' . $this->callableName);
        $this->description  = is_null($jobAnnotation->description)
                            ? 'No description is defined'
                            : $jobAnnotation->description;

        $this
            ->loadSettings($jobAnnotation, $defaultSettings)
            ->loadServers($jobAnnotation, $servers);
    }


    /**
     * Load settings
     * 
     * @param JobAnnotation $jobAnnotation JobAnnotation class
     * @param array         $servers       Array of servers defined for Worker
     * 
     * @return JobClass self Object
     */
    private function loadServers(JobAnnotation $jobAnnotation, array $servers)
    {
        /**
         * By default, this job takes default servers defined in its worker
         */
        $this->servers = $servers;

        /**
         * If is configured some servers definition in the worker, overwrites
         */
        if ($jobAnnotation->servers) {

            $this->servers  = ( is_array($jobAnnotation->servers) && !isset($jobAnnotation->servers['host']) )
                            ? $jobAnnotation->servers
                            : array($jobAnnotation->servers);
        }

        return $this;
    }


    /**
     * Load settings
     * 
     * @param WorkAnnotation $JobAnnotation   JobAnnotation class
     * @param array          $defaultSettings Default settings for Worker
     * 
     * @return JobClass self Object
     */
    private function loadSettings(JobAnnotation $jobAnnotation, array $defaultSettings)
    {
        $this->iterations   = is_null($jobAnnotation->iterations)
                            ? (int) $defaultSettings['iterations']
                            : $jobAnnotation->iterations;


        $this->defaultMethod    = is_null($jobAnnotation->defaultMethod)
                                ? $defaultSettings['method']
                                : $jobAnnotation->defaultMethod;

        return $this;
    }


    /**
     * Retrieve all Job data in cache format
     *
     * @return array
     */
    public function toArray()
    {
        return array(

            'callableName'          =>  $this->callableName,
            'methodName'            =>  $this->methodName,
            'realCallableName'      =>  $this->realCallableName,
            'description'           =>  $this->description,

            'iterations'			=>  $this->iterations,
            'servers'               =>  $this->servers,
            'defaultMethod'         =>  $this->defaultMethod,
        );
    }
}
