<?php
/**
 * Gearman annotations driver
 *
 * @author Marc Morera <yuhu@mmoreram.com>
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
