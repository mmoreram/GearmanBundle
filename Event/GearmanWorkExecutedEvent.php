<?php

namespace Mmoreram\GearmanBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

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
     * @param int $iterationsRemaining Iterations Remaining
     * @param int $returnCode Return code
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
