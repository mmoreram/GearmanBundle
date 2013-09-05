<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Service;

use Mmoreram\GearmanBundle\Service\Abstracts\AbstractGearmanService;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Gearman execute methods. All Worker methods
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanExecute extends AbstractGearmanService
{

    /**
     * @var Container
     *
     * Container instance
     */
    private $container;


    /**
     * Set container
     *
     * @param Container $container Container
     *
     * @return GearmanExecute self Object
     */
    public function setContainer(Container $container)
    {

        $this->container = $container;
    }


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
     * 
     * @return GearmanExecute self Object
     */
    private function callJob(Array $worker)
    {
        $gearmanWorker = new \GearmanWorker;

        if (isset($worker['job'])) {

            $jobs = array($worker['job']);
            $iterations = $worker['job']['iterations'];
            $this->addServers($gearmanWorker, $worker['job']['servers']);

        } else {

            $jobs = $worker['jobs'];
            $iterations = $worker['iterations'];
            $this->addServers($gearmanWorker, $worker['servers']);
        }

        $objInstance = $this->createJob($worker);
        $this->runJob($gearmanWorker, $objInstance, $jobs, $iterations);

        return $this;
    }


    /**
     * Given a worker settings, return Job instance
     * 
     * @param array $worker Worker settings
     * 
     * @return Object Job instance
     */
    private function createJob(array $worker)
    {
        /**
         * If service is defined, we must retrieve this class with dependency injection
         * 
         * Otherwise we just create it with a simple new()
         */
        if ($worker['service']) {

            $objInstance = $this->container->get($worker['service']);

        } else {

            $objInstance = new $worker['className'];

            /**
             * If instance of given object is instanceof ContainerAwareInterface, we inject full container
             *  by calling container setter.
             * 
             * @see https://github.com/mmoreram/gearman-bundle/pull/12
             */
            if ($objInstance instanceof ContainerAwareInterface) {

                $objInstance->setContainer($this->container);
            }
        }

        return $objInstance;
    }


    /**
     * Given a GearmanWorker and an instance of Job, run it
     * 
     * @param \GearmanWorker $gearmanWorker Gearman Worker
     * @param Object         $objInstance   Job instance
     * @param array          $jobs          Array of jobs to subscribe
     * @param integer        $iterations    Number of iterations
     * 
     * @return GearmanExecute self Object
     */
    private function runJob(\GearmanWorker $gearmanWorker, $objInstance, array $jobs, $iterations)
    {

        /**
         * Every job defined in worker is added into GearmanWorker
         */
        foreach ($jobs as $job) {

            $gearmanWorker->addFunction($job['realCallableName'], array($objInstance, $job['methodName']));
        }

        /**
         * If iterations value is 0, is like worker will never die
         */
        $alive = ( 0 == $iterations );

        /**
         * Executes GearmanWorker with all jobs defined
         */
        while ($gearmanWorker->work()) {

            if ($gearmanWorker->returnCode() != GEARMAN_SUCCESS) {

                break;
            }

            /**
             * Only finishes its execution if alive is false and iterations arrives to 0
             */
            if (!$alive && $iterations-- <= 0) {

                break;
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

                $gmworker->addServer($server['host'], $server['port']);
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
