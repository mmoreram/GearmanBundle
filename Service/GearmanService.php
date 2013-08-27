<?php

namespace Mmoreram\GearmanBundle\Service;

use Mmoreram\GearmanBundle\Service\GearmanCache as Cache;
use Mmoreram\GearmanBundle\Exceptions\JobDoesNotExistException;
use Mmoreram\GearmanBundle\Exceptions\WorkerDoesNotExistException;

/**
 * Gearman execute methods. All Worker methods
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
abstract class GearmanService
{

    /**
     * All workers
     *
     * @var type
     */
    protected $workers;


    /**
     * Construct method
     *
     * @param GearmanCacheWrapper $gearmanCacheWrapper GearmanCacheWrapper
     */
    public function __construct(GearmanCacheWrapper $gearmanCacheWrapper)
    {
        $this->workers = $gearmanCacheWrapper->getWorkerCollection();
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
        foreach ($this->workers as $worker) {

            if ($workerName === $worker['callableName']) {

                return $worker;
            }
        }

        throw new WorkerDoesNotExistException($workerName);
    }
}
