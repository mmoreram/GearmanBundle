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

namespace Mmoreram\GearmanBundle\Service\Abstracts;

use Mmoreram\GearmanBundle\Exceptions\JobDoesNotExistException;
use Mmoreram\GearmanBundle\Exceptions\WorkerDoesNotExistException;
use Mmoreram\GearmanBundle\Service\GearmanCacheWrapper;

/**
 * Gearman execute methods. All Worker methods
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
abstract class AbstractGearmanService
{
    /**
     * All workers
     *
     * @var array
     */
    protected $workers;

    /**
     * The prefix for all job names
     *
     * @var string $jobPrefix
     */
    protected $jobPrefix = null;

    /**
     * Construct method
     *
     * @param GearmanCacheWrapper $gearmanCacheWrapper GearmanCacheWrapper
     * @param array               $defaultSettings     The default settings for the bundle
     */
    public function __construct(GearmanCacheWrapper $gearmanCacheWrapper, array $defaultSettings)
    {
        $this->workers = $gearmanCacheWrapper->getWorkers();

        if (isset($defaultSettings['job_prefix'])) {

            $this->jobPrefix = $defaultSettings['job_prefix'];
        }
    }

    /**
     * Return worker containing a job with $jobName as name
     * If is not found, throws JobDoesNotExistException Exception
     *
     * @param string $jobName Name of job
     *
     * @return array
     *
     * @throws JobDoesNotExistException
     */
    public function getJob($jobName)
    {
        $jobName = $this->jobPrefix . $jobName;

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

        throw new JobDoesNotExistException();
    }

    /**
     * Return worker with $workerName as name and all its jobs
     * If is not found, throws WorkerDoesNotExistException Exception
     *
     * @param string $workerName Name of worker
     *
     * @return array
     *
     * @throws WorkerDoesNotExistException
     */
    public function getWorker($workerName)
    {
        foreach ($this->workers as $worker) {

            if ($workerName === $worker['callableName']) {
                return $worker;
            }
        }

        throw new WorkerDoesNotExistException();
    }

    /**
     * Return array of workers
     *
     * @return array all available workers
     */
    public function getWorkers()
    {
        return $this->workers;
    }
}
