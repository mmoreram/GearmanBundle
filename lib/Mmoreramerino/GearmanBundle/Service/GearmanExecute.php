<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Mmoreramerino\GearmanBundle\Service\GearmanService,
    Mmoreramerino\GearmanBundle\Workers\WorkerInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Gearman execute methods. All Worker methods
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanExecute extends GearmanService
{
    /**
     * Save original exception handler
     *
     * @var $symfonyExceptionHandler Object
     */
    protected $symfonyExceptionHandler;

    /**
     * Save original error handler
     *
     * @var $symfonyExceptionHandler Object
     */
    protected $symfonyErrorHandler;

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

        if ($objInstance instanceof ContainerAwareInterface && $this->container) {
            $objInstance->setContainer($this->container);
        }

        foreach ($jobs as $job) {
            $gmworker->addFunction($job['realCallableName'], array($objInstance, $job['methodName']));
        }

        $shouldStop = ($iterations > 0) ? true : false;
        try {
            if ($objInstance instanceof WorkerInterface) {
                $this->registerExecutionHandlers($objInstance);
            }
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
        } catch (\Exception $e) {
            if ($objInstance instanceof WorkerInterface) {
                $objInstance->exceptionHandler($e);
                $this->unregisterExecutionHandlers($objInstance);
            }
            throw $e;
        }

        if ($objInstance instanceof WorkerInterface) {
            $this->unregisterExecutionHandlers($objInstance);
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
     * Register execution handlers
     *
     * @param $instance Instance of Worker Class
     */
    private function registerExecutionHandlers($instance)
    {
        $this->symfonyExceptionHandler = set_exception_handler(array($instance, 'exceptionHandler'));
        $this->symfonyErrorHandler     = set_error_handler(array($instance, 'errorHandler'));
    }

    /**
     * Unregister execution handlers
     */
    private function unregisterExecutionHandlers()
    {
        set_exception_handler($this->symfonyExceptionHandler);
        set_error_handler($this->symfonyErrorHandler);
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
