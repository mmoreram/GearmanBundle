<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Mmoreramerino\GearmanBundle\Service\GearmanService;

/**
 * Gearman execute methods. All Worker methods
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanExecute extends GearmanService
{

    /**
     * Executes a job given a jobName and given settings and annotations of job
     *
     * @param string $jobName Name of job to be executed
     */
    public function executeJob($jobName)
    {
        $worker = $this->getJob($jobName);

        if (false !== $worker) {
            $this->callJob($worker);
        }
    }

    /**
     * Given a worker, execute GearmanWorker function defined by job.
     *
     * @param array $worker Worker definition
     */
    private function callJob(Array $worker)
    {
        $gmworker= new \GearmanWorker();
        if (isset($worker['job'])) {

            $jobs = array($worker['job']);
            $iterations = isset($worker['job']['iterations']) ? (int) ($worker['job']['iterations']) : 0;
            $this->addServers($gmworker, $worker['job']['servers']);

        } else {

            $jobs = $worker['jobs'];
            $iterations = isset($worker['iterations']) ? (int) ($worker['iterations']) : 0;
            $this->addServers($gmworker, $worker['servers']);
        }




        if (null !== $worker['service']) {
            $objInstance = $this->container->get($worker['service']);
        } else {
            $objInstance = new $worker['className'];
        }

        foreach ($jobs as $job) {
            $gmworker->addFunction($job['realCallableName'], array($objInstance, $job['methodName']));
        }

        $shouldStop = ($iterations > 0) ? true : false;

        while ($gmworker->work()) {

            if ($gmworker->returnCode() != GEARMAN_SUCCESS) {
                break;
            }

            if ($shouldStop) {
                $iterations--;
                if ($iterations <= 0) {
                    break;
                }
            }
        }
    }

    /**
     * Adds into worker all defined Servers.
     * If any is defined, performs default method
     *
     * @param \GearmanWorker $gmworker Worker to perform configuration
     * @param array          $servers  Servers array
     */
    private function addServers(\GearmanWorker $gmworker, Array $servers)
    {
        if (!empty($servers)) {

            foreach ($servers as $server) {
                list($addr, $port) = explode(':', $server, 2);
                $gmworker->addServer($addr, $port);
            }
        } else {
            $gmworker->addServer();
        }
    }

    /**
     * Executes a worker given a workerName subscribing all his jobs inside and given settings and annotations of worker and jobs
     *
     * @param string $workerName Name of worker to be executed
     */
    public function executeWorker($workerName)
    {
        $worker = $this->getWorker($workerName);

        if (false !== $worker) {
            $this->callJob($worker);
        }
    }
}
