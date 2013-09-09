<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Module;

use Mmoreram\GearmanBundle\Driver\Gearman\Job as JobAnnotation;
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
     * Default description when is not defined
     */
    const DEFAULT_DESCRIPTION = 'No description is defined';


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
     * @param JobAnnotation    $jobAnnotation     JobAnnotation class
     * @param ReflectionMethod $reflectionMethod  ReflextionMethod class
     * @param string           $callableNameClass Callable name class
     * @param array            $servers           Array of servers defined for Worker
     * @param array            $defaultSettings   Default settings for Worker
     */
    public function __construct(JobAnnotation $jobAnnotation, ReflectionMethod $reflectionMethod, $callableNameClass, array $servers, array $defaultSettings)
    {
        $this->callableName = is_null($jobAnnotation->name)
                            ? $reflectionMethod->getName()
                            : $jobAnnotation->name;

        $this->methodName = $reflectionMethod->getName();

        $this->realCallableName = str_replace('\\', '', $callableNameClass . '~' . $this->callableName);
        $this->description  = is_null($jobAnnotation->description)
                            ? self::DEFAULT_DESCRIPTION
                            : $jobAnnotation->description;

        $this->servers = $this->loadServers($jobAnnotation, $servers);
        $this->iterations = $this->loadIterations($jobAnnotation, $defaultSettings);
        $this->defaultMethod = $this->loadDefaultMethod($jobAnnotation, $defaultSettings);
    }


    /**
     * Load servers
     * 
     * If any server is defined in JobAnnotation, this one is used.
     * Otherwise is used servers set in Class
     * 
     * @param JobAnnotation $jobAnnotation JobAnnotation class
     * @param array         $servers       Array of servers defined for Worker
     * 
     * @return array Servers
     */
    private function loadServers(JobAnnotation $jobAnnotation, array $servers)
    {

        /**
         * If is configured some servers definition in the worker, overwrites
         */
        if ($jobAnnotation->servers) {

            $servers    = ( is_array($jobAnnotation->servers) && !isset($jobAnnotation->servers['host']) )
                        ? $jobAnnotation->servers
                        : array($jobAnnotation->servers);
        }

        return $servers;
    }


    /**
     * Load iterations
     * 
     * If iterations is defined in JobAnnotation, this one is used.
     * Otherwise is used set in Class
     * 
     * @param JobAnnotation $jobAnnotation   JobAnnotation class
     * @param array         $defaultSettings Default settings for Worker
     * 
     * @return integer Iteration
     */
    private function loadIterations(JobAnnotation $jobAnnotation, array $defaultSettings)
    {

        return  is_null($jobAnnotation->iterations)
                ? (int) $defaultSettings['iterations']
                : (int) $jobAnnotation->iterations;
    }


    /**
     * Load defaultMethod
     * 
     * If defaultMethod is defined in JobAnnotation, this one is used.
     * Otherwise is used set in Class
     * 
     * @param JobAnnotation $jobAnnotation   JobAnnotation class
     * @param array         $defaultSettings Default settings for Worker
     * 
     * @return string Default method
     */
    private function loadDefaultMethod(JobAnnotation $jobAnnotation, array $defaultSettings)
    {

        return  is_null($jobAnnotation->defaultMethod)
                ? $defaultSettings['method']
                : $jobAnnotation->defaultMethod;
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
