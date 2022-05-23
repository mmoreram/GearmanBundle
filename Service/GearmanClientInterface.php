<?php

namespace Mmoreram\GearmanBundle\Service;


use Mmoreram\GearmanBundle\Dispatcher\GearmanCallbacksDispatcher;
use Mmoreram\GearmanBundle\Exceptions\JobDoesNotExistException;
use Mmoreram\GearmanBundle\Exceptions\WorkerDoesNotExistException;
use Mmoreram\GearmanBundle\Generator\UniqueJobIdentifierGenerator;
use Mmoreram\GearmanBundle\Module\JobStatus;

/**
 * GearmanClient. Implementation of AbstractGearmanService
 *
 * @since 2.3.1
 */
interface GearmanClientInterface
{
    /**
     * @return \GearmanClient
     */
    public function getNativeClient();

    /**
     * Set  default servers
     *
     * @param array $defaultServers Default servers
     *
     * @return GearmanClient self Object
     */
    public function setDefaultServers(array $defaultServers);

    /**
     * Set UniqueJobIdentifierGenerator object
     *
     * @param UniqueJobIdentifierGenerator $uniqueJobIdentifierGenerator Unique Job Intefier Generator
     *
     * @return GearmanClient self Object
     */
    public function setUniqueJobIdentifierGenerator(UniqueJobIdentifierGenerator $uniqueJobIdentifierGenerator);

    /**
     * Set gearman callbacks
     *
     * @param GearmanCallbacksDispatcher $gearmanCallbacksDispatcher Gearman callbacks dispatcher
     *
     * @return GearmanClient self Object
     */
    public function setGearmanCallbacksDispatcher(GearmanCallbacksDispatcher $gearmanCallbacksDispatcher);

    /**
     * Set default settings
     *
     * @param array $settings
     *
     * @return GearmanClient self Object
     */
    public function setDefaultSettings($settings);

    /**
     * Set server to client. Empty all servers and set this one
     *
     * @param string $servername Server name (must be ip)
     * @param int $port Port of server. By default 4730
     *
     * @return GearmanClient Returns self object
     */
    public function setServer($servername, $port = 4730);

    /**
     * Add server to client
     *
     * @param string $servername Server name (must be ip)
     * @param int $port Port of server. By default 4730
     *
     * @return GearmanClient Returns self object
     */
    public function addServer($servername, $port = 4730);

    /**
     * Clear server list
     *
     * @return GearmanClient Returns self object
     */
    public function clearServers();


    /**
     * Runs a single task and returns some result, depending of method called.
     * Method called depends of default callable method setted on gearman
     * settings or overwritted on work or job annotations
     *
     * @param string $name A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return mixed result depending of method called.
     */
    public function callJob($name, $params = '', $unique = null);

    /**
     * Runs a single task and returns a string representation of the result.
     * It is up to the GearmanClient and GearmanWorker to agree on the format of
     * the result.
     *
     * The GearmanClient::do() method is deprecated as of pecl/gearman 1.0.0.
     * Use GearmanClient::doNormal().
     *
     * @param string $name A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string A string representing the results of running a task.
     * @deprecated
     */
    public function doJob($name, $params = '', $unique = null);

    /**
     * Runs a single task and returns a string representation of the result.
     * It is up to the GearmanClient and GearmanWorker to agree on the format of
     * the result.
     *
     * @param string $name A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string A string representing the results of running a task.
     */
    public function doNormalJob($name, $params = '', $unique = null);

    /**
     * Runs a task in the background, returning a job handle which can be used
     * to get the status of the running task.
     *
     * @param string $name A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string Job handle for the submitted task.
     */
    public function doBackgroundJob($name, $params = '', $unique = null);

    /**
     * Runs a single high priority task and returns a string representation of
     * the result.
     *
     * It is up to the GearmanClient and GearmanWorker to agree on the format of
     * the result.
     *
     * High priority tasks will get precedence over normal and low priority
     * tasks in the job queue.
     *
     * @param string $name A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string A string representing the results of running a task.
     */
    public function doHighJob($name, $params = '', $unique = null);

    /**
     * Runs a high priority task in the background, returning a job handle which
     * can be used to get the status of the running task.
     *
     * High priority tasks take precedence over normal and low priority tasks in
     * the job queue.
     *
     * @param string $name A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string The job handle for the submitted task.
     */
    public function doHighBackgroundJob($name, $params = '', $unique = null);

    /**
     * Runs a single low priority task and returns a string representation of
     * the result.
     *
     * It is up to the GearmanClient and GearmanWorker to agree on the format of
     * the result.
     *
     * Normal and high priority tasks will get precedence over low priority
     * tasks in the job queue.
     *
     * @param string $name A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string A string representing the results of running a task.
     */
    public function doLowJob($name, $params = '', $unique = null);

    /**
     * Runs a low priority task in the background, returning a job handle which
     * can be used to get the status of the running task.
     *
     * Normal and high priority tasks will get precedence over low priority
     * tasks in the job queue.
     *
     * @param string $name A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string The job handle for the submitted task.
     */
    public function doLowBackgroundJob($name, $params = '', $unique = null);

    /**
     * Fetches the Status of a special Background Job.
     *
     * @param string $idJob The job handle string
     *
     * @return JobStatus Job status
     */
    public function getJobStatus($idJob);

    /**
     * Gets the return code from the last run job.
     *
     * @return int
     */
    public function getReturnCode();

    /**
     * Adds a task to be run in parallel with other tasks.
     * Call this method for all the tasks to be run in parallel, then call
     * GearmanClient::runTasks() to perform the work.
     *
     * Note that enough workers need to be available for the tasks to all run in
     * parallel.
     *
     * @param string $name A GermanBundle registered function to be executed
     * @param string $params Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTask($name, $params = '', &$context = null, $unique = null);

    /**
     * Adds a high priority task to be run in parallel with other tasks.
     * Call this method for all the high priority tasks to be run in parallel,
     * then call GearmanClient::runTasks() to perform the work.
     *
     * Tasks with a high priority will be selected from the queue before those
     * of normal or low priority.
     *
     * @param string $name A GermanBundle registered function to be executed
     * @param string $params Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTaskHigh($name, $params = '', &$context = null, $unique = null);

    /**
     * Adds a low priority background task to be run in parallel with other
     * tasks.
     *
     * Call this method for all the tasks to be run in parallel, then call
     * GearmanClient::runTasks() to perform the work.
     *
     * Tasks with a low priority will be selected from the queue after those of
     * normal or low priority.
     *
     * @param string $name A GermanBundle registered function to be executed
     * @param string $params Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTaskLow($name, $params = '', &$context = null, $unique = null);

    /**
     * Adds a background task to be run in parallel with other tasks
     * Call this method for all the tasks to be run in parallel, then call
     * GearmanClient::runTasks() to perform the work.
     *
     * @param string $name A GermanBundle registered function to be executed
     * @param string $params Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTaskBackground($name, $params = '', &$context = null, $unique = null);

    /**
     * Adds a high priority background task to be run in parallel with other
     * tasks.
     *
     * Call this method for all the tasks to be run in parallel, then call
     * GearmanClient::runTasks() to perform the work.
     *
     * Tasks with a high priority will be selected from the queue before those
     * of normal or low priority.
     *
     * @param string $name A GermanBundle registered function to be executed
     * @param string $params Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTaskHighBackground($name, $params = '', &$context = null, $unique = null);

    /**
     * Adds a low priority background task to be run in parallel with other
     * tasks.
     *
     * Call this method for all the tasks to be run in parallel, then call
     * GearmanClient::runTasks() to perform the work.
     *
     * Tasks with a low priority will be selected from the queue after those of
     * normal or high priority.
     *
     * @param string $name A GermanBundle registered function to be executed
     * @param string $params Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTaskLowBackground($name, $params = '', &$context = null, $unique = null);

    /**
     * For a set of tasks previously added with
     *
     * GearmanClient::addTask(),
     * GearmanClient::addTaskHigh(),
     * GearmanClient::addTaskLow(),
     * GearmanClient::addTaskBackground(),
     * GearmanClient::addTaskHighBackground(),
     * GearmanClient::addTaskLowBackground(),
     *
     * this call starts running the tasks in parallel.
     * Note that enough workers need to be available for the tasks to all run in parallel
     *
     * @return boolean run tasks result
     */
    public function runTasks();
}