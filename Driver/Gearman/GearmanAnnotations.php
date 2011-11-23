<?php

namespace Mmoreramerino\GearmanBundle\Driver\Gearman;

use Doctrine\Common\Annotations\Annotation;

/**
 * Gearman annotations driver
 * 
 * @author Marc Morera <marc@ulabox.com>
 */

/**
 * @Annotation
 */
final class Work extends Annotation {
    /**
     * Number of iterations specified for all jobs inside Work
     *
     * @var integer
     */
    public $name = NULL;
    
    /**
     * Description of Worker
     *
     * @var string
     */
    public $description = NULL;
    
    /**
     * Number of iterations specified for all jobs inside Work
     *
     * @var integer
     */
    public $iter = NULL;
    
    /**
     * Servers assigned for all jobs of this work to be executed
     *
     * @var mixed
     */
    public $servers = null;
}

/**
 * @Annotation
 */
final class Job extends Annotation {
    /**
     * Method name to assign into job
     *
     * @var string
     */
    public $name = NULL;
    
    /**
     * Description of Job
     *
     * @var string
     */
    public $description = NULL;
    
    /**
     * Number of iterations specified for this job
     *
     * @var integer
     */
    public $iter = NULL;
    
    /**
     * Servers assigned for this job to be executed
     *
     * @var mixed
     */
    public $servers = null;
}