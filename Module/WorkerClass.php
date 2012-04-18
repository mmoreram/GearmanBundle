<?php

namespace Mmoreramerino\GearmanBundle\Module;

use Doctrine\Common\Annotations\Reader;
use Mmoreramerino\GearmanBundle\Driver\Gearman\Work;
use Mmoreramerino\GearmanBundle\Module\JobCollection;
use Mmoreramerino\GearmanBundle\Module\JobClass as Job;
use Mmoreramerino\GearmanBundle\Exceptions\SettingValueMissingException;
use Mmoreramerino\GearmanBundle\Exceptions\SettingValueBadFormatException;

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
     * Callable name for this job.
     * If is setted on annotations, this value will be used.
     * Otherwise, natural method name will be used.
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
     * @param Work             $classAnnotation ClassAnnotation class
     * @param \ReflectionClass $reflectionClass Reflexion class
     * @param Reader           $reader          ReaderAnnotation class
     * @param array            $settings        Settings array
     */
    public function __construct(Work $classAnnotation, \ReflectionClass $reflectionClass, Reader $reader, array $settings)
    {
        $this->namespace = $reflectionClass->getNamespaceName();

        $this->callableName =   str_replace('\\', '', ((null !== $classAnnotation->name) ?
            ($this->namespace .'\\' .$classAnnotation->name) :
            $reflectionClass->getName()));

        $this->description =    (null !== $classAnnotation->description) ?
            $classAnnotation->description :
            'No description is defined';

        $this->fileName = $reflectionClass->getFileName();
        $this->className = $reflectionClass->getName();
        $this->service = $classAnnotation->service;

        if (!isset($settings['defaults'])) {
            throw new SettingValueMissingException('defaults');
        }

        if (isset($settings['defaults']['iterations']) && null !== $settings['defaults']['iterations']) {
            $iter = (int) ($settings['defaults']['iterations']);

            if (null !== $classAnnotation->iterations) {
                $iter = (int) ($classAnnotation->iterations);
            }
        } else {
            throw new SettingValueMissingException('defaults/iterations');
        }
        $this->iterations = $iter;

        /**
         * Servers definition for worker
         */
        $servers = array();
        if (isset($settings['defaults']['servers']) && null !== $settings['defaults']['servers']) {
            if (is_array($settings['defaults']['servers'])) {

                foreach ($settings['defaults']['servers'] as $name => $server) {
                    $servername = $server['hostname'].':'.(int) ($server['port']);
                    $servers[$name] = $servername;
                }
            } else {

                throw new SettingValueBadFormatException('servers');
            }

            if (null !== $classAnnotation->servers) {
                if (is_array($classAnnotation->servers)) {
                    $servers = $classAnnotation->servers;
                } else {
                    $servers = array($classAnnotation->servers);
                }
            }
        } else {
            throw new SettingValueMissingException('defaults/servers');
        }
        $this->servers = $servers;

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
            'service'       =>  $this->service,
            'servers'       =>  $this->servers,
            'iterations'    =>  $this->iterations,
            'jobs'          =>  array(),
        );

        $dump['jobs'] = $this->jobCollection->__toCache();

        return $dump;
    }

}
