<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Service;

use Mmoreram\GearmanBundle\Service\Abstracts\AbstractGearmanService;
use Mmoreram\GearmanBundle\GearmanMethods;
use Mmoreram\GearmanBundle\Module\JobStatus;

/**
 * GearmanClient. Implementation of AbstractGearmanService
 */
class GearmanClient extends AbstractGearmanService
{

    /**
     * @var GearmanCallbacks
     * 
     * Gearman callbacks
     */
    protected $gearmanCallbacks;


    /**
     * @var array
     * 
     * Server set to define in what server must connect to
     */
    protected $servers = array();


    /**
     * @var array
     * 
     * task structure to store all about called tasks
     */
    protected $taskStructure = array();


    /**
     * @var array
     * 
     * Set default servers
     */
    protected $defaultServers;

    /**
     * @var array
     *
     * Set default settings
     */
    protected $settings;


    /**
     * Set  default servers
     * 
     * @param array $defaultServers Default servers
     * 
     * @return GearmanClient self Object
     */
    public function setDefaultServers(array $defaultServers)
    {
        $this->defaultServers = $defaultServers;

        return $this;
    }


    /**
     * Set gearman callbacks
     * 
     * @param GearmanCallbacks $gearmanCallbacks Gearman callbacks
     * 
     * @return GearmanClient self Object
     */
    public function setGearmanCallbacks(GearmanCallbacks $gearmanCallbacks)
    {
        $this->gearmanCallbacks = $gearmanCallbacks;

        return $this;
    }

    /**
     * Set default settings
     *
     * @param array $settings
     *
     * @return GearmanClient self Object
     */
    public function setDefaultSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }


    /**
     * Set server to client. Empty all servers and set this one
     *
     * @param type $servername Server name (must be ip)
     * @param type $port       Port of server. By default 4730
     *
     * @return GearmanClient Returns self object
     */
    public function setServer($servername, $port = 4730)
    {
        $this
            ->clearServers()
            ->addServer($servername, $port);

        return $this;
    }


    /**
     * Add server to client
     *
     * @param type $servername Server name (must be ip)
     * @param type $port       Port of server. By default 4730
     *
     * @return GearmanClient Returns self object
     */
    public function addServer($servername, $port = 4730)
    {
        $this->servers[] = array(
            'host'  =>  $servername,
            'port'  =>  $port,
        );

        return $this;
    }


    /**
     * Clear server list
     *
     * @return GearmanClient Returns self object
     */
    public function clearServers()
    {
        $this->servers = array();

        return $this;
    }


    /**
     * Get real worker from job name and enqueues the action given one
     *     method.
     *
     * @param string $jobName A GearmanBundle registered function the worker is to execute
     * @param string $params  Parameters to send to job as string
     * @param string $method  Method to execute
     * @param string $unique  A unique ID used to identify a particular task
     *
     * @return mixed Return result of the call
     */
    private function enqueue($jobName, $params = '', $method = null, $unique = null)
    {
        $worker = $this->getJob($jobName);

        if ($this->settings['generate_unique_key']) {

            $unique = $this->generateUniqueKey($jobName, $params);
        }

        if (false !== $worker) {

            return $this->doEnqueue($worker, $params, $method, $unique);
        }

        return false;
    }


    /**
     * Execute a GearmanClient call given a worker, params and a method.
     *
     * If he GarmanClient call is asyncronous, result value will be a handler.
     * Otherwise, will return job result.
     *
     * @param array  $worker Worker definition
     * @param string $params Parameters to send to job as string
     * @param string $method Method to execute
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return mixed  Return result of the GearmanClient call
     */
    private function doEnqueue(Array $worker, $params = '', $method = null, $unique = null)
    {
        $gearmanClient = new \GearmanClient();
        $this->assignServers($gearmanClient);

        return $gearmanClient->$method($worker['job']['realCallableName'], $params, $unique);
    }


    /**
     * Given a GearmanClient, set all included servers
     *
     * @param GearmanClient $gearmanClient Object to include servers
     *
     * @return GearmanClient Returns self object
     */
    private function assignServers(\GearmanClient $gearmanClient)
    {
        $servers = $this->defaultServers;

        if (!empty($this->servers)) {

            $servers = $this->servers;
        }

        /**
         * We include each server into gearman client
         */
        foreach ($servers as $server) {

            $gearmanClient->addServer($server['host'], $server['port']);
        }

        return $this;
    }


    /**
     * Job methods
     */

    /**
     * Runs a single task and returns some result, depending of method called.
     * Method called depends of default callable method setted on gearman settings
     *  or overwritted on work or job annotations
     *
     * @param string $name   A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return mixed result depending of method called.
     */
    public function callJob($name, $params = '', $unique = null)
    {
       $worker = $this->getJob($name);
       $methodCallable = $worker['job']['defaultMethod'] . 'Job';

       return $this->enqueue($name, $params, $methodCallable, $unique);
    }


    /**
     * Runs a single task and returns a string representation of the result.
     * It is up to the GearmanClient and GearmanWorker to agree on the format of the result.
     * The GearmanClient::do() method is deprecated as of pecl/gearman 1.0.0. Use GearmanClient::doNormal().
     *
     * @param string $name   A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string A string representing the results of running a task.
     * @deprecated
     */
    public function doJob($name, $params = '', $unique = null)
    {

        return $this->enqueue($name, $params, GearmanMethods::GEARMAN_METHOD_DO, $unique);
    }


    /**
     * Runs a single task and returns a string representation of the result.
     * It is up to the GearmanClient and GearmanWorker to agree on the format of the result.
     *
     * @param string $name   A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string A string representing the results of running a task.
     */
    public function doNormalJob($name, $params = '', $unique = null)
    {

        return $this->enqueue($name, $params, GearmanMethods::GEARMAN_METHOD_DONORMAL, $unique);
    }


    /**
     * Runs a task in the background, returning a job handle which
     *     can be used to get the status of the running task.
     *
     * @param string $name   A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string Job handle for the submitted task.
     */
    public function doBackgroundJob($name, $params = '', $unique = null)
    {

        return $this->enqueue($name, $params, GearmanMethods::GEARMAN_METHOD_DOBACKGROUND, $unique);
    }


    /**
     * Runs a single high priority task and returns a string representation of the result.
     * It is up to the GearmanClient and GearmanWorker to agree on the format of the result.
     * High priority tasks will get precedence over normal and low priority tasks in the job queue.
     *
     * @param string $name   A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string A string representing the results of running a task.
     */
    public function doHighJob($name, $params = '', $unique = null)
    {

        return $this->enqueue($name, $params, GearmanMethods::GEARMAN_METHOD_DOHIGH, $unique);
    }


    /**
     * Runs a high priority task in the background, returning a job handle which can be used to get the status of the running task.
     * High priority tasks take precedence over normal and low priority tasks in the job queue.
     *
     * @param string $name   A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string The job handle for the submitted task.
     */
    public function doHighBackgroundJob($name, $params = '', $unique = null)
    {

        return $this->enqueue($name, $params, GearmanMethods::GEARMAN_METHOD_DOHIGHBACKGROUND, $unique);
    }


    /**
     * Runs a single low priority task and returns a string representation of the result.
     * It is up to the GearmanClient and GearmanWorker to agree on the format of the result.
     * Normal and high priority tasks will get precedence over low priority tasks in the job queue.
     *
     * @param string $name   A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string A string representing the results of running a task.
     */
    public function doLowJob($name, $params = '', $unique = null)
    {

        return $this->enqueue($name, $params, GearmanMethods::GEARMAN_METHOD_DOLOW, $unique);
    }


    /**
     * Runs a low priority task in the background, returning a job handle which can be used to get the status of the running task.
     * Normal and high priority tasks will get precedence over low priority tasks in the job queue.
     *
     * @param string $name   A GearmanBundle registered function the worker is to execute
     * @param string $params Parameters to send to job as string
     * @param string $unique A unique ID used to identify a particular task
     *
     * @return string The job handle for the submitted task.
     */
    public function doLowBackgroundJob($name, $params = '', $unique = null)
    {

        return $this->enqueue($name, $params, GearmanMethods::GEARMAN_METHOD_DOLOWBACKGROUND, $unique);
    }


    /**
     * Fetches the Status of a special Background Job.
     *
     * @param string $idJob The job handle string
     * 
     * @return JobStatus Job status
     */
    public function getJobStatus($idJob)
    {
        $gearmanClient = new \GearmanClient();
        $this->assignServers($gearmanClient);
        $statusData = $gearmanClient->jobStatus($idJob);

        $jobStatus = new JobStatus($statusData);

        return $jobStatus;
    }


    /**
     * Task methods
     */


    /**
     * Adds a task to be run in parallel with other tasks.
     * Call this method for all the tasks to be run in parallel, then call GearmanClient::runTasks() to perform the work.
     * Note that enough workers need to be available for the tasks to all run in parallel.
     *
     * @param string $name     A GermanBundle registered function to be executed
     * @param string $params   Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique   A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTask($name, $params = '', &$context = null, $unique = null)
    {
        $this->enqueueTask($name, $params, $context, $unique, GearmanMethods::GEARMAN_METHOD_ADDTASK);

        return $this;
    }


    /**
     * Adds a high priority task to be run in parallel with other tasks.
     * Call this method for all the high priority tasks to be run in parallel, then call GearmanClient::runTasks() to perform the work.
     * Tasks with a high priority will be selected from the queue before those of normal or low priority.
     *
     * @param string $name     A GermanBundle registered function to be executed
     * @param string $params   Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique   A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTaskHigh($name, $params = '', &$context = null, $unique = null)
    {
        $this->enqueueTask($name, $params, $context, $unique, GearmanMethods::GEARMAN_METHOD_ADDTASKHIGH);

        return $this;
    }


    /**
     * Adds a low priority background task to be run in parallel with other tasks.
     * Call this method for all the tasks to be run in parallel, then call GearmanClient::runTasks() to perform the work.
     * Tasks with a low priority will be selected from the queue after those of normal or low priority.
     *
     * @param string $name     A GermanBundle registered function to be executed
     * @param string $params   Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique   A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTaskLow($name, $params = '', &$context = null, $unique = null)
    {
        $this->enqueueTask($name, $params, $context, $unique, GearmanMethods::GEARMAN_METHOD_ADDTASKLOW);

        return $this;
    }


    /**
     * Adds a background task to be run in parallel with other tasks
     * Call this method for all the tasks to be run in parallel, then call GearmanClient::runTasks() to perform the work.
     *
     * @param string $name     A GermanBundle registered function to be executed
     * @param string $params   Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique   A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTaskBackground($name, $params = '', &$context = null, $unique = null)
    {
        $this->enqueueTask($name, $params, $context, $unique, GearmanMethods::GEARMAN_METHOD_ADDTASKBACKGROUND);

        return $this;
    }


    /**
     * Adds a high priority background task to be run in parallel with other tasks.
     * Call this method for all the tasks to be run in parallel, then call GearmanClient::runTasks() to perform the work.
     * Tasks with a high priority will be selected from the queue before those of normal or low priority.
     *
     * @param string $name     A GermanBundle registered function to be executed
     * @param string $params   Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique   A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTaskHighBackground($name, $params = '', &$context = null, $unique = null)
    {
        $this->enqueueTask($name, $params, $context, $unique, GearmanMethods::GEARMAN_METHOD_ADDTASKHIGHBACKGROUND);

        return $this;
    }


    /**
     * Adds a low priority background task to be run in parallel with other tasks.
     * Call this method for all the tasks to be run in parallel, then call GearmanClient::runTasks() to perform the work.
     * Tasks with a low priority will be selected from the queue after those of normal or high priority.
     *
     * @param string $name     A GermanBundle registered function to be executed
     * @param string $params   Parameters to send to task as string
     * @param Mixed  &$context Application context to associate with a task
     * @param string $unique   A unique ID used to identify a particular task
     *
     * @return GearmanClient Return this object
     */
    public function addTaskLowBackground($name, $params = '', &$context = null, $unique = null)
    {
        $this->enqueueTask($name, $params, $context, $unique, GearmanMethods::GEARMAN_METHOD_ADDTASKLOWBACKGROUND);

        return $this;
    }


    /**
     * Adds a task into the structure of tasks with included type of call
     *
     * @param string $name    A GermanBundle registered function to be executed
     * @param string $params  Parameters to send to task as string
     * @param Mixed  $context Application context to associate with a task
     * @param string $unique  A unique ID used to identify a particular task
     * @param string $method  Method to perform
     *
     * @return GearmanClient Return this object
     */
    private function enqueueTask($name, $params, $context, $unique, $method)
    {
        if ($this->settings['generate_unique_key']) {

            $unique = $this->generateUniqueKey($name, $params);
        }

        $task = array(
            'name'      =>  $name,
            'params'    =>  $params,
            'context'   =>  $context,
            'unique'    =>  $unique,
            'method'    =>  $method,
        );

        $this->addTaskToStructure($task);

        return $this;
    }

    /**
     * Appends a task structure into taskStructure array
     *
     * @param array $task Task structure
     *
     * @return GearmanClient Return this object
     */
    private function addTaskToStructure(array $task)
    {
        $this->taskStructure[] = $task;

        return $this;
    }


    /**
     * For a set of tasks previously added with GearmanClient::addTask(), GearmanClient::addTaskHigh(),
     * GearmanClient::addTaskLow(), GearmanClient::addTaskBackground(), GearmanClient::addTaskHighBackground(),
     * or GearmanClient::addTaskLowBackground(), this call starts running the tasks in parallel.
     * Note that enough workers need to be available for the tasks to all run in parallel
     *
     * @return boolean run tasks result
     */
    public function runTasks()
    {
        $gearmanClient = new \GearmanClient();
        $this->assignServers($gearmanClient);

        if ($this->settings['callbacks']) {
            $this->gearmanCallbacks->assignTaskCallbacks($gearmanClient);
        }

        foreach ($this->taskStructure as $task) {

            $type = $task['method'];
            $jobName = $task['name'];
            $worker = $this->getJob($jobName);

            if (false !== $worker) {

                $gearmanClient->$type($worker['job']['realCallableName'], $task['params'], $task['context'], $task['unique']);
            }
        }

        $this->taskStructure = array();

        return $gearmanClient->runTasks();
    }


    /**
     * Generates a unique string if null
     *
     * @param string $name   Name of the Worker/Job 
     * @param string $params Params
     *
     * @return string Unique string generated for given object and params
     */
    private function generateUniqueKey($name, $params = '')
    {
        return md5($name . $params);
    }
}
