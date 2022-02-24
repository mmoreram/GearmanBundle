<?php

namespace Mmoreram\GearmanBundle\Driver\Gearman;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Job extends Annotation
{

    /**
     * Description of Job
     */
    public ?string $description;

    /**
     * Number of iterations specified for this job
     */
    public ?int $iterations;

    /**
     * Servers assigned for this job to be executed
     *
     * @var mixed
     */
    public $servers;

    /**
     * Default method to call for this job
     */
    public ?string $defaultMethod;
    public ?int $timeout;
    /**
     * Number of seconds the execution must run before being allowed to terminate
     */
    public ?int $minimumExecutionTime;
}
