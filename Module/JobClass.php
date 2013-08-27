<?php

namespace Mmoreram\GearmanBundle\Module;

use Mmoreram\GearmanBundle\Driver\Gearman\Job;
use Mmoreram\GearmanBundle\Driver\Gearman\Work;
use Symfony\Component\DependencyInjection\ContainerAware;
use Mmoreram\GearmanBundle\Exceptions\SettingValueMissingException;
use Mmoreram\GearmanBundle\Exceptions\SettingValueBadFormatException;

/**
 * Job class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
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
    private $callableName;


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
     * @param Job               $methodAnnotation  MethodAnnotation class
     * @param \ReflectionMethod $method            ReflextionMethod class
     * @param Work              $classAnnotation   Work class
     * @param string            $callableNameClass Callable name class
     * @param array             $settings          Settings structure
     */
    public function __construct( Job $methodAnnotation, \ReflectionMethod $method, $callableNameClass, array $servers, array $defaultSettings)
    {
        $this->callableName = is_null($methodAnnotation->name)
                            ? $method->getName()
                            : $methodAnnotation->name;

        $this->methodName = $method->getName();

        $this->realCallableName = str_replace('\\', '', $callableNameClass . '~' . $this->callableName);
        $this->description  = is_null($methodAnnotation->description)
                            ? 'No description is defined'
                            : $methodAnnotation->description;


        $this->iterations   = is_null($methodAnnotation->iterations)
                            ? (int) $defaultSettings['iterations']
                            : $methodAnnotation->iterations;


        $this->defaultMethod    = is_null($classAnnotation->defaultMethod)
                                ? $defaultSettings['method']
                                : $classAnnotation->defaultMethod;

        
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
