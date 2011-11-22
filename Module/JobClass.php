<?php

namespace Ulabox\GearmanBundle\Module;

use Ulabox\GearmanBundle\Driver\Gearman\Job;
use Ulabox\GearmanBundle\Driver\Gearman\Work;
use Ulabox\GearmanBundle\Exceptions\SettingValueMissingException;

/**
 * Job class
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
class JobClass
{    
    /**
     * Callable name for this job
     * If is setted on annotations, this value will be used
     *  otherwise, natural method name will be used.
     *
     * @var string
     */
    private $callableName;
    
    
    /**
     * Description of Job
     *
     * @var string
     */
    private $description;
    
    public function __construct( Job $methodAnnotation, \ReflectionMethod $method, Work $classAnnotation, $callableNameClass, array $settings)
    {
        $this->callableName =   (null !== $methodAnnotation->name) ?
                                    $methodAnnotation->name :
                                    $method->getName();
        
        $this->methodName   =   $method->getName();
        
        $this->realCallableName = $callableNameClass.'~'.$this->callableName;
        $this->description  =    (null !== $method->getDocComment()) ?
                                    $methodAnnotation->description :
                                    'No description is defined';
        /**
         * Iterations definition for job
         */        
        if (null !== $settings['defaults']['iter']) {
            $iter = (int)($settings['defaults']['iter']);
            
            if (null !== $classAnnotation->iter) {
                $iter = (int)($classAnnotation->iter);
            }
            
            if (null !== $methodAnnotation->iter) {
                $iter = (int)($methodAnnotation->iter);
            }
        } else {
            throw new SettingValueMissingException('defaults/iter');
        }
        $this->iter = $iter;
        
        /**
         * Servers definition for job
         */
        if (null !== $settings['defaults']['servers']) {
            if (is_array($settings['defaults']['servers'])) {
                $servers = $settings['defaults']['servers'];
            } else {
                $servers = array($settings['defaults']['servers']);
            }
            
            if (null !== $classAnnotation->servers) {
                if (is_array($classAnnotation->servers)) {
                    $servers = $classAnnotation->servers;
                } else {
                    $servers = array($classAnnotation->servers);
                }
            }
            
            if (null !== $methodAnnotation->servers) {
                if (is_array($methodAnnotation->servers)) {
                    $servers = $methodAnnotation->servers;
                } else {
                    $servers = array($methodAnnotation->servers);
                }
            }
        } else {
            throw new SettingValueMissingException('defaults/servers');
        }
        $this->servers = $servers;
    }
    
    /**
     * Retrieve all Job data in cache format
     *
     * @return array 
     */
    public function __toCache()
    {
        return array(
            'callableName'          =>  $this->callableName,
            'methodName'            =>  $this->methodName,
            'realCallableName'      =>  $this->realCallableName,
            'description'           =>  $this->description,
            'iter'                  =>  $this->iter,
            'servers'               =>  $this->servers,
        );
    }
}
