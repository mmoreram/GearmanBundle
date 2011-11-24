<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Mmoreramerino\GearmanBundle\Service\GearmanService;

/**
 * Gearman execute methods. All Worker methods
 *
 * @author Marc Morera <marc@ulabox.com>
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
        $worker = $this->getWorker($jobName);

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
        $settings = $this->getSettings();
        $job = $worker['job'];

        if (is_array($job['servers'])) {

            foreach ($job['servers'] as $server) {
                list($addr, $port) = explode(':', $server, 2);
                $gmworker->addServer($addr, $port);
            }
        } else {
            $gmworker->addServer();
        }
        
        
        if (null !== $worker['service']) {
            $objInstance = $this->container->get($worker['service']);
        } else {
            $objInstance = new $worker['className'];
        }
        
        $gmworker->addFunction($job['realCallableName'], array($objInstance, $job['methodName']));

        $iter = isset($job['iter']) ? (int) ($job['iter']) : 0;
        $shouldStop = ($iter > 0) ? true : false;

        while ($gmworker->work()) {

            if ($gmworker->returnCode() != GEARMAN_SUCCESS) {
                break;
            }

            if ($shouldStop) {
                $iter--;
                if ($iter <= 0) {
                    break;
                }
            }
        }
    }
}
