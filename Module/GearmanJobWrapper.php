<?php

/**
 * Gearman Bundle for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Janusz Paszyński <jpaszynski@have2code.com>
 */

namespace Mmoreram\GearmanBundle\Module;


use Mmoreram\GearmanBundle\Event\Worker\JobCompleteEvent;
use Mmoreram\GearmanBundle\Event\Worker\JobDataEvent;
use Mmoreram\GearmanBundle\Event\Worker\JobExceptionEvent;
use Mmoreram\GearmanBundle\Event\Worker\JobFailEvent;
use Mmoreram\GearmanBundle\Event\Worker\JobReturnEvent;
use Mmoreram\GearmanBundle\Event\Worker\JobStartEvent;
use Mmoreram\GearmanBundle\Event\Worker\JobStatusEvent;
use Mmoreram\GearmanBundle\Event\Worker\JobWarningEvent;
use Mmoreram\GearmanBundle\GearmanEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * {@see \GearmanJob} observable wrapper class. Each method used for sending any kind of information to client
 * emits Symfony Kernel Event.
 * @package Mmoreram\GearmanBundle\Module
 * @since 3.1.0
 */
class GearmanJobWrapper
{
    /** @var EventDispatcherInterface */
    protected $dispatcher;
    /** @var \GearmanJob */
    protected $job;

    /**
     * GearmanJobWrapper constructor.
     * @param EventDispatcherInterface $dispatcher dispatcher to be used for events emission
     * @param \GearmanJob $job Gearmna job instance that shall be passed to worker
     */
    public function __construct(EventDispatcherInterface $dispatcher, \GearmanJob $job)
    {
        $this->dispatcher = $dispatcher;
        $this->job = $job;

        // dispatch start event
        $this->dispatcher->dispatch(GearmanEvents::GEARMAN_WORKER_JOB_START, new JobStartEvent($this->job));
    }

    /**
     * Returns the last return code issued by the job server.
     *
     * @link http://php.net/manual/en/gearmanjob.returncode.php
     * @return int A valid Gearman return code
     */
    public function returnCode()
    {
        return $this->job->returnCode();
    }

    /**
     * Sets the return value for this job, indicates how the job completed.
     *
     * Method causes {@see \Mmoreram\GearmanBundle\GearmanEvents::GEARMAN_WORKER_JOB_RETURN GEARMAN_WORKER_JOB_RETURN}
     * event to be emmited just before calling {@see \GearmanJob::setReturn()} method on wrapped {@see \GearmanJob}
     * instance.
     *
     * @param string $gearman_return_t a valid Gearman return value
     * @return bool
     */
    public function setReturn($gearman_return_t)
    {
        $event = (new JobReturnEvent($this->job))
            ->setReturnValue($gearman_return_t);
        $this->dispatcher->dispatch(GearmanEvents::GEARMAN_WORKER_JOB_RETURN, $event);
        return $this->job->setReturn($gearman_return_t);
    }

    /**
     * Sends data to the job server (and any listening clients) for this job.
     *
     * Method causes {@see \Mmoreram\GearmanBundle\GearmanEvents::GEARMAN_WORKER_JOB_DATA GEARMAN_WORKER_JOB_DATA}
     * event to be emmited just before calling {@see \GearmanJob::sendData()} method on wrapped {@see \GearmanJob}
     * instance.
     *
     * @param string $data Arbitrary serialized data
     * @return bool
     */
    public function sendData($data)
    {
        $event = (new JobDataEvent($this->job))
            ->setData($data);
        $this->dispatcher->dispatch(GearmanEvents::GEARMAN_WORKER_JOB_DATA, $event);
        return $this->job->sendData($data);
    }

    /**
     * Sends a warning for this job while it is running.
     *
     * Method causes {@see \Mmoreram\GearmanBundle\GearmanEvents::GEARMAN_WORKER_JOB_WARNING GEARMAN_WORKER_JOB_WARNING}
     * event to be emmited just before calling {@see \GearmanJob::sendWarning()} method on wrapped {@see \GearmanJob}
     * instance.
     *
     * @param string $warning A warning messages
     * @return bool
     */
    public function sendWarning($warning)
    {
        $event = (new JobWarningEvent($this->job))
            ->setMessage($warning);
        $this->dispatcher->dispatch(GearmanEvents::GEARMAN_WORKER_JOB_WARNING, $event);
        return $this->job->sendWarning($warning);
    }

    /**
     * Sends status information to the job server and any listening clients. Use this to specify what percentage of the job has been completed.
     *
     * Method causes {@see \Mmoreram\GearmanBundle\GearmanEvents::GEARMAN_WORKER_JOB_STATUS GEARMAN_WORKER_JOB_STATUS}
     * event to be emmited just before calling {@see \GearmanJob::sendStatus()} method on wrapped {@see \GearmanJob}
     * instance.
     *
     * @param int $numerator The numerator of the precentage completed expressed as a fraction
     * @param int $denominator The denominator of the precentage completed expressed as a fraction
     * @return bool
     */
    public function sendStatus($numerator, $denominator)
    {
        $event = (new JobStatusEvent($this->job))
            ->setNumerator($numerator)
            ->setDenumerator($denominator);
        $this->dispatcher->dispatch(GearmanEvents::GEARMAN_WORKER_JOB_STATUS, $event);
        return $this->job->sendStatus($numerator, $denominator);
    }

    /**
     * Sends result data and the complete status update for this job.
     *
     * Method causes {@see \Mmoreram\GearmanBundle\GearmanEvents::GEARMAN_WORKER_JOB_COMPLETE GEARMAN_WORKER_JOB_COMPLETE}
     * event to be emmited just before calling {@see \GearmanJob::sendComplete()} method on wrapped {@see \GearmanJob}
     * instance.
     *
     * @param string $result Serialized result data
     * @return bool
     */
    public function sendComplete($result)
    {
        $event = (new JobCompleteEvent($this->job))
            ->setResult($result);
        $this->dispatcher->dispatch(GearmanEvents::GEARMAN_WORKER_JOB_COMPLETE, $event);
        return $this->job->sendComplete($result);
    }

    /**
     * Sends the supplied exception when this job is running.
     *
     * Method causes {@see \Mmoreram\GearmanBundle\GearmanEvents::GEARMAN_WORKER_JOB_EXCEPTION GEARMAN_WORKER_JOB_EXCEPTION}
     * event to be emmited just before calling {@see \GearmanJob::sendException()} method on wrapped {@see \GearmanJob}
     * instance.
     *
     * @param string $exception An exception description
     * @return bool
     */
    public function sendException($exception)
    {
        $event = (new JobExceptionEvent($this->job))
            ->setException($exception);
        $this->dispatcher->dispatch(GearmanEvents::GEARMAN_WORKER_JOB_EXCEPTION, $event);
        return $this->job->sendException($exception);
    }

    /**
     * Sends failure status for this job, indicating that the job failed in a known way (as opposed to failing due to a thrown exception).
     *
     * Method causes {@see \Mmoreram\GearmanBundle\GearmanEvents::GEARMAN_WORKER_JOB_FAIL GEARMAN_WORKER_JOB_FAIL}
     * event to be emmited just before calling {@see \GearmanJob::sendException()} method on wrapped {@see \GearmanJob}
     * instance.
     *
     * @return bool
     */
    public function sendFail()
    {
        $this->dispatcher->dispatch(GearmanEvents::GEARMAN_WORKER_JOB_FAIL, new JobFailEvent($this->job));
        return $this->job->sendFail();
    }

    /**
     * Returns the opaque job handle assigned by the job server.
     *
     * @link http://php.net/manual/en/gearmanjob.handle.php
     * @return string An opaque job handle
     */
    public function handle()
    {
        return $this->job->handle();
    }

    /**
     * Returns the function name for this job. This is the function the work will
     * execute to perform the job.
     *
     * @link http://php.net/manual/en/gearmanjob.functionname.php
     * @return string The name of a function
     */
    public function functionName()
    {
        return $this->job->functionName();
    }

    /**
     * Returns the unique identifiter for this job. The identifier is assigned by the
     * client.
     *
     * @link http://php.net/manual/en/gearmanjob.unique.php
     * @return string An opaque unique identifier
     */
    public function unique()
    {
        return $this->job->unique();
    }

    /**
     * Returns the workload for the job. This is serialized data that is to be
     * processed by the worker.
     *
     * @link http://php.net/manual/en/gearmanjob.workload.php
     * @return string Serialized data
     */
    public function workload()
    {
        return $this->job->workload();
    }

    /**
     * Returns the size of the job's work load (the data the worker is to process) in
     * bytes.
     *
     * @link http://php.net/manual/en/gearmanjob.workloadsize.php
     * @return int The size in bytes
     */
    public function workloadSize()
    {
        return $this->job->workloadSize();
    }
}
