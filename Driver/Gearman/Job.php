<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Driver\Gearman;

use Doctrine\Common\Annotations\Annotation;


/**
 * Gearman Job annotation driver
 * 
 * @Annotation
 */
class Job extends Annotation
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