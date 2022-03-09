<?php

namespace Mmoreram\GearmanBundle\Module;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;

use Mmoreram\GearmanBundle\Driver\Gearman\Job as JobAnnotation;
use Mmoreram\GearmanBundle\Driver\Gearman\Work as WorkAnnotation;
use Mmoreram\GearmanBundle\Module\JobClass as Job;

class WorkerClass
{

    public const DEFAULT_DESCRIPTION = 'No description is defined';
    private string $namespace;
    private string $className;
    private string $fileName;
    private string $callableName;

    /**
     *
     * Service alias if this worker is wanted to be built by dependency injection
     */
    private ?string $service;

    /**
     * Description of Job
     */
    private string $description;

    /**
     * Number of iterations this job will be alive before die
     */
    private int $iterations;

    /**
     * Default method this job will be call into Gearman client
     */
    private string $defaultMethod;

    /**
     * Job minimum execution time
     */
    private int $minimumExecutionTime;

    /**
     * Timeout for idle job
     */
    private int $timeout;

    /**
     * Collection of servers to connect
     */
    private array $servers;

    /**
     * All jobs inside Worker
     */
    private JobCollection $jobCollection;

    /**
     * The prefix for all job names
     */
    private ?string $jobPrefix;

    /**
     * Retrieves all jobs available from worker
     *
     */
    public function __construct(
        WorkAnnotation $workAnnotation,
        ReflectionClass $reflectionClass,
        Reader $reader,
        array $servers,
        array $defaultSettings
    ) {
        $this->namespace = $reflectionClass->getNamespaceName();

        /**
         * If WorkAnnotation name field is defined, workers_name_prepend_namespace value
         * in defaultSettings array must be checked.
         *
         * If true, namespace must be prepended to workAnnotation name for callableName
         * Otherwise, only workAnnotation value is set as callableName
         */
        $callableNameNamespace = $defaultSettings['workers_name_prepend_namespace']
            ? $this->namespace
            : '';

        /**
         * Setting worker callable name
         */
        $this->callableName = is_null($workAnnotation->name)
            ? $reflectionClass->getName()
            : $callableNameNamespace . $workAnnotation->name;

        $this->callableName = str_replace('\\', '', $this->callableName);


        $this->description = $workAnnotation->description ?? self::DEFAULT_DESCRIPTION;

        $this->fileName = $reflectionClass->getFileName();
        $this->className = $reflectionClass->getName();
        $this->service = $workAnnotation->service;

        $this->jobPrefix = $defaultSettings['job_prefix'] ?? null;

        $this->servers = $this->loadServers($workAnnotation, $servers);
        $this->iterations = (int)($workAnnotation->iterations ?? $defaultSettings['iterations']);
        $this->defaultMethod = (string)($workAnnotation->defaultMethod ?? $defaultSettings['method']);
        $this->minimumExecutionTime = (int)($workAnnotation->minimumExecutionTime ?? $defaultSettings['minimum_execution_time']);
        $this->timeout = (int)($workAnnotation->timeout ?? $defaultSettings['timeout']);
        $this->jobCollection = $this->createJobCollection($reflectionClass, $reader);
    }

    /**
     * Load servers
     *
     * If any server is defined in JobAnnotation, this one is used.
     * Otherwise is used servers set in Class
     *
     * @param WorkAnnotation $workAnnotation WorkAnnotation class
     * @param array $servers Array of servers defined for Worker
     *
     * @return array Servers
     */
    private function loadServers(WorkAnnotation $workAnnotation, array $servers)
    {
        /**
         * If is configured some servers definition in the worker, overwrites
         */
        if ($workAnnotation->servers) {
            $servers = (is_array($workAnnotation->servers) && !isset($workAnnotation->servers['host']))
                ? $workAnnotation->servers
                : [$workAnnotation->servers];
        }

        return $servers;
    }

    private function createJobCollection(ReflectionClass $reflectionClass, Reader $reader): JobCollection
    {
        $jobCollection = new JobCollection();

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $methodAnnotations = $reader->getMethodAnnotations($reflectionMethod);

            foreach ($methodAnnotations as $methodAnnotation) {
                if ($methodAnnotation instanceof JobAnnotation) {
                    $job = new Job($methodAnnotation, $reflectionMethod, $this->callableName, $this->servers, [
                        'jobPrefix' => $this->jobPrefix,
                        'iterations' => $this->iterations,
                        'method' => $this->defaultMethod,
                        'minimumExecutionTime' => $this->minimumExecutionTime,
                        'timeout' => $this->timeout,
                    ]);

                    $jobCollection->add($job);
                }
            }
        }

        return $jobCollection;
    }

    public function toArray(): array
    {
        return [
            'namespace' => $this->namespace,
            'className' => $this->className,
            'fileName' => $this->fileName,
            'callableName' => $this->callableName,
            'description' => $this->description,
            'service' => $this->service,
            'servers' => $this->servers,
            'iterations' => $this->iterations,
            'minimumExecutionTime' => $this->minimumExecutionTime,
            'timeout' => $this->timeout,
            'jobs' => $this->jobCollection->toArray(),
        ];
    }
}