<?php

namespace Mmoreram\GearmanBundle\Module;

use ReflectionMethod;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mmoreram\GearmanBundle\Driver\Gearman\Job as JobAnnotation;

class JobClass implements ContainerAwareInterface
{
    public const DEFAULT_DESCRIPTION = 'No description is defined';

    /**
     * Callable name for this job
     * If is setted on annotations, this value will be used
     *  otherwise, natural method name will be used.
     */
    private string $callableName;
    private string $methodName;

    /**
     * RealCallable name for this job without the job prefix
     */
    private string $realCallableNameNoPrefix;

    /**
     * RealCallable name for this job
     * natural method name will be used.
     */
    private string $realCallableName;
    private string $description;
    private int $iterations;
    private string $defaultMethod;
    private int $minimumExecutionTime;
    private int $timeout;

    /**
     * @var array
     *
     * Collection of servers to connect
     */
    private $servers;
    private ?string $jobPrefix;
    protected ?ContainerInterface $container;

    public function __construct(
        JobAnnotation $jobAnnotation,
        ReflectionMethod $reflectionMethod,
        string $callableNameClass,
        array $servers,
        array $defaultSettings
    ) {
        $this->callableName = $jobAnnotation->name ?? $reflectionMethod->getName();
        $this->methodName = $reflectionMethod->getName();
        $this->realCallableNameNoPrefix = str_replace('\\', '', $callableNameClass . '~' . $this->callableName);

        $this->jobPrefix = $defaultSettings['jobPrefix'] ?? null;

        $this->realCallableName = $this->jobPrefix . $this->realCallableNameNoPrefix;
        $this->description = $jobAnnotation->description ?? self::DEFAULT_DESCRIPTION;
        $this->servers = $this->loadServers($jobAnnotation, $servers);
        $this->iterations = (int)($jobAnnotation->iterations ?? $defaultSettings['iterations']);
        $this->defaultMethod = (string)($jobAnnotation->defaultMethod ?? $defaultSettings['method']);
        $this->minimumExecutionTime = (int)($jobAnnotation->minimumExecutionTime ?? $defaultSettings['minimumExecutionTime']);
        $this->timeout = (int)($jobAnnotation->timeout ?? $defaultSettings['timeout']);
    }

    /**
     * Load servers
     *
     * If any server is defined in JobAnnotation, this one is used.
     * Otherwise is used servers set in Class
     *
     * @param JobAnnotation $jobAnnotation JobAnnotation class
     * @param array $servers Array of servers defined for Worker
     *
     * @return array Servers
     */
    private function loadServers(JobAnnotation $jobAnnotation, array $servers)
    {
        /**
         * If is configured some servers definition in the worker, overwrites
         */
        if ($jobAnnotation->servers) {
            $servers = (is_array($jobAnnotation->servers) && !isset($jobAnnotation->servers['host']))
                ? $jobAnnotation->servers
                : [$jobAnnotation->servers];
        }

        return $servers;
    }

    public function toArray(): array
    {
        return [

            'callableName' => $this->callableName,
            'methodName' => $this->methodName,
            'realCallableName' => $this->realCallableName,
            'jobPrefix' => $this->jobPrefix,
            'realCallableNameNoPrefix' => $this->realCallableNameNoPrefix,
            'description' => $this->description,
            'iterations' => $this->iterations,
            'minimumExecutionTime' => $this->minimumExecutionTime,
            'timeout' => $this->timeout,
            'servers' => $this->servers,
            'defaultMethod' => $this->defaultMethod,
        ];
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
