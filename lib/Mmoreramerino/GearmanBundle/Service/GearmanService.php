<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Mmoreramerino\GearmanBundle\MmoreramerinoGearmanBundle;
use Mmoreramerino\GearmanBundle\Exceptions\JobDoesNotExistException;
use Mmoreramerino\GearmanBundle\Exceptions\WorkerDoesNotExistException;
use Mmoreramerino\GearmanBundle\Module\WorkerClass;

/**
 * Gearman execute methods. All Worker methods
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanService extends GearmanSettings
{

    /**
     * All workers
     *
     * @var WorkerClass[]
     */
    protected $workers = null;

    /**
     * Retrieve all Workers from cache
     * Return $workers
     *
     * @return WorkerClass[]
     */
    public function setWorkers()
    {
        if (!is_array($this->workers)) {

            $gearmanCache = $this->container->get(MmoreramerinoGearmanBundle::CACHE_SERVICE);
            $this->workers = $gearmanCache->fetch(MmoreramerinoGearmanBundle::CACHE_ID);
        }

        /**
         * Always will be an Array
         */

        return $this->workers;
    }

    /**
     * Return worker containing a job with $jobName as name
     * If is not found, throws JobDoesNotExistException Exception
     *
     * @param string $jobName Name of job
     *
     * @throws \Mmoreramerino\GearmanBundle\Exceptions\JobDoesNotExistException
     * @return WorkerClass
     */
    public function getJob($jobName)
    {
        $this->setWorkers();

        foreach ($this->workers as $worker) {
            if (is_array($worker->getJobCollection())) {
                foreach ($worker->getJobCollection() as $job) {
                    if ($jobName === $job->getRealCallableName()) {
                        $worker->setJob($job);

                        return $worker;
                    }
                }
            }
        }

        throw new JobDoesNotExistException($jobName);
    }

    /**
     * Return worker with $workerName as name and all its jobs
     * If is not found, throws WorkerDoesNotExistException Exception
     *
     * @param string $workerName Name of worker
     *
     * @throws \Mmoreramerino\GearmanBundle\Exceptions\WorkerDoesNotExistException
     * @return WorkerClass
     */
    public function getWorker($workerName)
    {
        $this->setWorkers();

        foreach ($this->workers as $worker) {
            if ($workerName === $worker->getCallableName()) {
                return $worker;
            }
        }

        throw new WorkerDoesNotExistException($workerName);
    }
}
