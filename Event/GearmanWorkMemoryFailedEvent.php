<?php


namespace Mmoreram\GearmanBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class GearmanWorkMemoryFailedEvent extends Event
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


    private $memoryLimit;
    private $memoryUsage;

    /**
     * Construct method
     *
     * @param array $jobs Jobs
     * @param int $iterationsRemaining Iterations Remaining
     */
    public function __construct(array $jobs, $iterationsRemaining, $memoryLimit, $memoryUsage)
    {
        $this->jobs = $jobs;
        $this->iterationsRemaining = $iterationsRemaining;
        $this->memoryLimit = $memoryLimit;
        $this->memoryUsage = $memoryUsage;
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
     * @return mixed
     */
    public function getMemoryLimit()
    {
        return $this->memoryLimit;
    }

    /**
     * @return mixed
     */
    public function getMemoryUsage()
    {
        return $this->memoryUsage;
    }


}