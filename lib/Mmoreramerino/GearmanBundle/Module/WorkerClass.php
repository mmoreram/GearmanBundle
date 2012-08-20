<?php

namespace Mmoreramerino\GearmanBundle\Module;

use Doctrine\Common\Annotations\Reader;
use Mmoreramerino\GearmanBundle\Driver\Gearman\Work;
use Mmoreramerino\GearmanBundle\Module\JobClass as Job;
use Mmoreramerino\GearmanBundle\Exceptions\SettingValueMissingException;
use Mmoreramerino\GearmanBundle\Exceptions\SettingValueBadFormatException;

/**
 * Worker class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class WorkerClass
{
    /**
     * All jobs inside Worker
     *
     * @var Job[]
     */
    private $jobCollection;

    /**
     * @var Job
     */
    private $job;

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
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $service;

    /**
     * @var array
     */
    private $servers;

    /**
     * @var int
     */
    private $iterations;

    /**
     * Retrieves all jobs available from worker
     *
     * @param Work             $classAnnotation ClassAnnotation class
     * @param \ReflectionClass $reflectionClass Reflexion class
     * @param Reader           $reader          ReaderAnnotation class
     * @param array            $settings        Settings array
     * @throws \Mmoreramerino\GearmanBundle\Exceptions\SettingValueBadFormatException
     * @throws \Mmoreramerino\GearmanBundle\Exceptions\SettingValueMissingException
     */
    public function init(Work $classAnnotation, \ReflectionClass $reflectionClass, Reader $reader, array $settings)
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

        $this->jobCollection = array();

        foreach ($reflectionClass->getMethods() as $method) {
            $reflMethod = new \ReflectionMethod($method->class, $method->name);
            $methodAnnotations = $reader->getMethodAnnotations($reflMethod);
            foreach ($methodAnnotations as $annot) {
                if ($annot instanceof \Mmoreramerino\GearmanBundle\Driver\Gearman\Job) {
                    $job = new Job();
                    $job->init($annot, $reflMethod, $classAnnotation, $this->callableName, $settings);
                    $this->jobCollection[] = $job;
                }
            }
        }
    }

    public static function __set_state(array $data)
    {
        $worker = new WorkerClass;
        $worker->namespace     = $data['namespace'];
        $worker->className     = $data['className'];
        $worker->fileName      = $data['fileName'];
        $worker->callableName  = $data['callableName'];
        $worker->description   = $data['description'];
        $worker->service       = $data['service'];
        $worker->servers       = $data['servers'];
        $worker->iterations    = $data['iterations'];
        $worker->jobCollection = $data['jobCollection'];

        return $worker;
    }

    /**
     * @return string
     */
    public function getCallableName()
    {
        return $this->callableName;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return int
     */
    public function getIterations()
    {
        return $this->iterations;
    }

    /**
     * @return Job[]
     */
    public function getJobCollection()
    {
        return $this->jobCollection;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param \Mmoreramerino\GearmanBundle\Module\JobClass $job
     */
    public function setJob($job)
    {
        $this->job = $job;
    }

    /**
     * @return \Mmoreramerino\GearmanBundle\Module\JobClass
     */
    public function getJob()
    {
        return $this->job;
    }
}
