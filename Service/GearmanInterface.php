<?php

namespace Mmoreramerino\GearmanBundle\Service;

/**
 * Interface of how a Gearman class should be implemented
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
interface GearmanInterface
{    
    /**
     * Runs a single task and returns a string representation of the result. 
     * It is up to the GearmanClient and GearmanWorker to agree on the format of the result. 
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string A string representing the results of running a task. 
     */
    public function doJob($name, $params);
    
    
    /**
     * Runs a task in the background, returning a job handle which 
     *     can be used to get the status of the running task. 
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string Job handle for the submitted task.
     */
    public function doBackgroundJob($name, $params);
    
    
    /**
     * Runs a single high priority task and returns a string representation of the result. 
     * It is up to the GearmanClient and GearmanWorker to agree on the format of the result. 
     * High priority tasks will get precedence over normal and low priority tasks in the job queue. 
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string A string representing the results of running a task. 
     */
    public function doHighJob($name, $params);
    
    /**
     * Runs a high priority task in the background, returning a job handle which can be used to get the status of the running task.
     * High priority tasks take precedence over normal and low priority tasks in the job queue. 
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string The job handle for the submitted task.
     */
    public function doHighBackgroundJob($name, $params);
    
    /**
     * Runs a single low priority task and returns a string representation of the result. 
     * It is up to the GearmanClient and GearmanWorker to agree on the format of the result. 
     * Normal and high priority tasks will get precedence over low priority tasks in the job queue.
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string A string representing the results of running a task. 
     */
    public function doLowJob($name, $params);
    
    /**
     * Runs a low priority task in the background, returning a job handle which can be used to get the status of the running task.
     * Normal and high priority tasks will get precedence over low priority tasks in the job queue.
     * 
     * @param string $name A GearmanBundle registered function the worker is to execute 
     * @params Mixed $params
     * 
     * @return string The job handle for the submitted task.
     */
    public function doLowBackgroundJob($name, $params);
}
