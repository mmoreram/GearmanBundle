<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Mmoreramerino\GearmanBundle\MmoreramerinoGearmanBundle;
use Mmoreramerino\GearmanBundle\Exceptions\JobDoesNotExistException;
use Mmoreramerino\GearmanBundle\Exceptions\WorkerDoesNotExistException;
use Mmoreramerino\GearmanBundle\Module\WorkerClass;
use Doctrine\Common\Cache\Cache;

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
     * Init workers. If they're not loaded yet - init them
     * Return $workers
     *
     * @return WorkerClass[]
     */
    protected function initWorkers()
    {
        if (!$this->workers) {

            /** @var Cache $cache  */
            $cache = $this->container->get(MmoreramerinoGearmanBundle::CACHE_SERVICE);
            $existsCache = $cache->contains(MmoreramerinoGearmanBundle::CACHE_ID);

            $cacheclearEnvs = array(
                'back_dev', 'back_test', 'dev', 'test',
            );

            if (in_array($this->container->get('kernel')->getEnvironment(), $cacheclearEnvs) || !$existsCache) {
                if ($existsCache) {
                    $cache->delete(MmoreramerinoGearmanBundle::CACHE_ID);
                }

                /** @var GearmanLoader $gearmanCacheLoader  */
                $gearmanCacheLoader = $this->container->get('gearman.loader');
                $gearmanCacheLoader->load($cache);
            }
            $this->workers = $cache->fetch(MmoreramerinoGearmanBundle::CACHE_ID);
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
        $this->initWorkers();

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
        $this->initWorkers();

        foreach ($this->workers as $worker) {
            if ($workerName === $worker->getCallableName()) {
                return $worker;
            }
        }

        throw new WorkerDoesNotExistException($workerName);
    }

    /**
     * @return WorkerClass[]
     */
    public function getWorkers() {
        $this->initWorkers();
        return $this->workers;
    }
}
