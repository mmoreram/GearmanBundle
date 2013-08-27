<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Mmoreramerino\GearmanBundle\Service\GearmanService;
use Mmoreramerino\GearmanBundle\Module\WorkerClass;

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
     * @param WorkerClass $worker Worker definition
     */
    private function callJob(WorkerClass $worker)
    {
        $gmworker= new \GearmanWorker();
        $jobs = $worker->getJobCollection();
        $iterations = (int) $worker->getIterations() ?: 0;
        $this->addServers($gmworker, $worker->getServers());

        if (null !== $worker->getService()) {
            $objInstance = $this->container->get($worker->getService());
        } else {
            $className = $worker->getClassName();
            $objInstance = new $className;
            if ($objInstance instanceof \Symfony\Component\DependencyInjection\ContainerAwareInterface) {
                $objInstance->setContainer($this->container);
            }
        }

        foreach ($jobs as $job) {
            $gmworker->addFunction($job->getRealCallableName(), array($objInstance, $job->getMethodName()));
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
    private function addServers(\GearmanWorker $gmworker, array $servers)
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
