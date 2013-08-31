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
     */
    private function callJob(Array $worker)
    {
        $gearmanWorker = new \GearmanWorker();

        if (isset($worker['job'])) {

            $jobs = array($worker['job']);
            $iterations = $worker['job']['iterations'];
            $this->addServers($gearmanWorker, $worker['job']['servers']);

        } else {

            $jobs = $worker['jobs'];
            $iterations = $worker['iterations'];
            $this->addServers($gearmanWorker, $worker['servers']);
        }


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


        /**
         * Every job defined in worker is added into GearmanWorker
         */
        foreach ($jobs as $job) {

            $gearmanWorker->addFunction($job['realCallableName'], array($objInstance, $job['methodName']));
        }


        /**
         * Executes GearmanWorker with all jobs defined
         */
        while ($gearmanWorker->work()) {

            if ($gearmanWorker->returnCode() != GEARMAN_SUCCESS) {

                break;
            }

            if ($iterations-- <= 0) {

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
