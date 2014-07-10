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
 * @author Dominic Grostate <codekestrel@googlemail.com>
 */

namespace Mmoreram\GearmanBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * GearmanWorkExecutedEvent
 *
 * @since 2.4.2
 */
class GearmanWorkExecutedEvent extends Event
{
    /**
     * @var array
     *
     * Gearman jobs running
     */
    protected $jobs;

    /**
     * @var int
     *
     * Remaining iterations on work
     */
    protected $iterationsRemaining;

    /**
     * @var int
     *
     * Return code from last ran job
     */
    protected $returnCode;

    /**
     * Construct method
     *
     * @param array $jobs Jobs
     * @param int   $iterationsRemaining Iterations Remaining
     * @param int   $returnCode Return code
     */
    public function __construct(array $jobs, $iterationsRemaining, $returnCode)
    {
        $this->jobs = $jobs;
        $this->iterationsRemaining = $iterationsRemaining;
        $this->returnCode = $returnCode;
    }

    /**
     * Get Gearman Work subscribed jobs
     *
     * @return array Subscribed jobs
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * Get Gearman Work remaining iteration length
     *
     * @return int Remaining iterations
     */
    public function getIterationsRemaining()
    {
        return $this->iterationsRemaining;
    }

    /**
     * Get Gearman Job return code
     *
     * @return int Last return code
     */
    public function getReturnCode()
    {
        return $this->returnCode;
    }
}