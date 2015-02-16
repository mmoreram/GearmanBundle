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
 * Gearman Work annotation driver
 *
 * @since 2.3.1
 *
 * @Annotation
 */
class Work extends Annotation
{
    /**
     * Name of worker
     *
     * @var integer
     */
    public $name;

    /**
     * Description of Worker
     *
     * @var string
     */
    public $description;

    /**
     * Number of iterations specified for all jobs inside Work
     *
     * @var integer
     */
    public $iterations;

    /**
     * Servers assigned for all jobs of this work to be executed
     *
     * @var mixed
     */
    public $servers;

    /**
     * Default method to call for all jobs inside this work
     *
     * @var string
     */
    public $defaultMethod;

    /**
     * Default timeout in seconds for worker idle time
     *
     * @var int
     */
    public $timeout;

    /**
     * @var int
     *
     * Default number of seconds the execution must run before being allowed to terminate
     */
    public $minimumExecutionTime;

    /**
     * Service typeof Class. If it's defined, object will be instanced throught
     * service dependence injection.
     * Otherwise, class will be instance with new() method
     *
     * @var string
     */
    public $service = null;
}
