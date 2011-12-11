<?php
/**
 * Gearman annotations driver
 *
 * @author Marc Morera <marc@ulabox.com>
 */

namespace Mmoreramerino\GearmanBundle\Driver\Gearman;

use Doctrine\Common\Annotations\Annotation;

/** @Annotation */
final class Work extends Annotation
{
    /**
     * Name of worker
     *
     * @var integer
     */
    public $name = null;

    /**
     * Description of Worker
     *
     * @var string
     */
    public $description = null;

    /**
     * Number of iterations specified for all jobs inside Work
     *
     * @var integer
     */
    public $iterations = null;

    /**
     * Servers assigned for all jobs of this work to be executed
     *
     * @var mixed
     */
    public $servers = null;

    /**
     * Default method to call for all jobs inside this work
     *
     * @var string
     */
    public $defaultMethod = null;

    /**
     * Service typeof Class. If it's defined, object will be instanced throught service dependence injection.
     * Otherwise, class will be instance with new() method
     *
     * @var string
     */
    public $service = null;
}

/** @Annotation */
final class Job extends Annotation
{
    /**
     * Method name to assign into job
     *
     * @var string
     */
    public $name = null;

    /**
     * Description of Job
     *
     * @var string
     */
    public $description = null;

    /**
     * Number of iterations specified for this job
     *
     * @var integer
     */
    public $iterations = null;

    /**
     * Servers assigned for this job to be executed
     *
     * @var mixed
     */
    public $servers = null;

    /**
     * Default method to call for this job
     *
     * @var string
     */
    public $defaultMethod = null;
}