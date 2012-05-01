<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Mmoreramerino\GearmanBundle\Service\GearmanCache as Cache;
use Mmoreramerino\GearmanBundle\Exceptions\JobDoesNotExistException;
use Mmoreramerino\GearmanBundle\Exceptions\WorkerDoesNotExistException;

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
     * @var type
     */
    protected $workers = null;

    /**
     * All settings
     *
     * @var settings
     */
    protected $settings = null;

    /**
     * Retrieve all Workers from cache
     * Return $workers
     *
     * @return Array
     */
    public function setWorkers()
    {
        if (!is_array($this->workers)) {

            $gearmanCache = $this->container->get('gearman.cache');
            $this->workers = $gearmanCache->get();
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
     * @return Array
     */
    public function getJob($jobName)
    {
        $this->setWorkers();

        foreach ($this->workers as $worker) {
            if (is_array($worker['jobs'])) {
                foreach ($worker['jobs'] as $job) {
                    if ($jobName === $job['realCallableName']) {
                        $worker['job'] = $job;

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
     * @return Array
     */
    public function getWorker($workerName)
    {
        $this->setWorkers();

        foreach ($this->workers as $worker) {
            if ($workerName === $worker['callableName']) {
                return $worker;
            }
        }

        throw new WorkerDoesNotExistException($workerName);
    }
}
