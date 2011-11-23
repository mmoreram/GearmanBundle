<?php

namespace Mmoreramerino\GearmanBundle\Module;

use Doctrine\Common\Annotations\AnnotationReader;
use Mmoreramerino\GearmanBundle\Driver\Gearman\Work;
use Mmoreramerino\GearmanBundle\Module\JobCollection;
use Mmoreramerino\GearmanBundle\Module\JobClass as Job;

/**
 * Worker class
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
class WorkerClass
{
    /**
     * All jobs inside Worker
     *
     * @var JobCollection
     */
    private $jobCollection;
    
    
    /**
     * Callable name for this job
     * If is setted on annotations, this value will be used
     *  otherwise, natural method name will be used.
     *
     * @var string
     */
    private $callableName;
    
    /**
     * Namespace of Work class
     *
     * @var string
     */
    private $namespace;
    
    /**
     * Retrieves all jobs available from worker
     *
     * @param Work $classAnnotation
     * @param \ReflectionClass $reflectionClass
     * @param AnnotationReader $reader
     * @param array $settings 
     */
    public function __construct( Work $classAnnotation, \ReflectionClass $reflectionClass, AnnotationReader $reader, array $settings)
    {
        $this->namespace = $reflectionClass->getNamespaceName();
        
        $this->callableName =   (null !== $classAnnotation->name) ?
                                $classAnnotation->name :
                                $this->namespace;
        
        $this->description =    (null !== $classAnnotation->description) ?
                                $classAnnotation->description :
                                'No description is defined';
        
        $this->fileName = $reflectionClass->getFileName();
        $this->className = $reflectionClass->getName();
        
        $this->jobCollection = new JobCollection;
        
        foreach ($reflectionClass->getMethods() as $method) {
            $reflMethod = new \ReflectionMethod($method->class, $method->name);
            $methodAnnotations = $reader->getMethodAnnotations($reflMethod);
            foreach ($methodAnnotations as $annot) {
                if ($annot instanceof \Mmoreramerino\GearmanBundle\Driver\Gearman\Job) {
                    $this->jobCollection->add(new Job($annot, $reflMethod, $classAnnotation, $this->callableName, $settings));
                }
            }
        }
    }
    
    /**
     * Retrieve all Worker data in cache format
     *
     * @return array
     */
    public function __toCache()
    {
        $dump = array(
            'namespace'     =>  $this->namespace,
            'className'     =>  $this->className,
            'fileName'      =>  $this->fileName,
            'callableName'  =>  $this->callableName,
            'description'   =>  $this->description,
            'jobs'          =>  array(),
        );
        
        $dump['jobs'] = $this->jobCollection->__toCache();
        
        return $dump;
    }
    
}