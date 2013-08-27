<?php

namespace Mmoreram\GearmanBundle\Module;

use Mmoreram\GearmanBundle\Module\JobClass as Job;

/**
 * Job Collection class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class JobCollection
{

    /**
     * @var array
     * 
     * All jobs from worker
     */
    private $workerJobs = array();


    /**
     * Adds into $workerJobs a Job instance
     * Return self object
     *
     * @param Job $workJob Class to add into array
     *
     * @return JobCollection
     */
    public function add(Job $workJob)
    {
        $this->workerJobs[] = $workJob;

        return $this;
    }


    /**
     * Retrieve all Jobs added previously
     *
     * @return array
     */
    public function getJobs()
    {
        return $this->workerJobs;
    }


    /**
     * Retrieve all jobs loaded previously in cache format
     *
     * @return array
     */
    public function toArray()
    {
        $jobs = array();

        foreach ($this->workerJobs as $job) {

            $jobs[] = $job->toArray();
        }

        return $jobs;
    }
}
