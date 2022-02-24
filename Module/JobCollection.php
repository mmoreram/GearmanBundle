<?php

namespace Mmoreram\GearmanBundle\Module;

use Mmoreram\GearmanBundle\Module\JobClass as Job;

class JobCollection
{
    private array $workerJobs = [];

    public function add(Job $workJob): self
    {
        $this->workerJobs[] = $workJob;

        return $this;
    }

    /**
     * @return Job[]
     */
    public function getJobs(): array
    {
        return $this->workerJobs;
    }

    public function toArray(): array
    {
        $jobs = [];

        foreach ($this->workerJobs as $job) {
            $jobs[] = $job->toArray();
        }

        return $jobs;
    }
}
