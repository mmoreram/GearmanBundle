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

namespace Mmoreram\GearmanBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use GearmanWorker;

/**
 * GearmanExecuteWorkEvent
 *
 * @since 2.4.2
 */
class GearmanExecuteWorkEvent extends Event
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
     * @param array $jobs
     * @param int $iterationsRemaining
     * @param int $returnCode
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