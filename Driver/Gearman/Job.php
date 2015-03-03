<?php

/**
 * Gearman Bundle for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Mmoreram\GearmanBundle\Driver\Gearman;

use Doctrine\Common\Annotations\Annotation;

/**
 * Gearman Job annotation driver
 *
 * @since 2.3.1
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
    public $name;

    /**
     * Description of Job
     *
     * @var string
     */
    public $description;

    /**
     * Number of iterations specified for this job
     *
     * @var integer
     */
    public $iterations;

    /**
     * Servers assigned for this job to be executed
     *
     * @var mixed
     */
    public $servers;

    /**
     * Default method to call for this job
     *
     * @var string
     */
    public $defaultMethod;

    /**
     * Timeout in seconds for job idle time
     *
     * @var int
     */
    public $timeout;

    /**
     * @var int
     *
     * Number of seconds the execution must run before being allowed to terminate
     */
    public $minimumExecutionTime;
}
