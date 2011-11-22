<?php

namespace Ulabox\GearmanBundle\Service;

use Ulabox\GearmanBundle\Service\GearmanService;

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
     * @param type $jobName 
     */
    public function executeJob($jobName)
    {
        $rootDir = $this->container->get('kernel')->getRootDir();
        $settingsPath = $rootDir . '/config/gearman_'.$this->container->get('kernel')->getEnvironment().'.yml';
        $this->loadSettings($settingsPath);
        
        $worker = $this->getWorker($jobName);
        
        if (false !== $worker) {
            $this->callJob($worker);
        }
    }
    
    /**
     * Given a worker, execute GearmanWorker function defined by job.
     *
     * @param array $worker 
     */
    private function callJob(Array $worker)
    {
        $gmworker= new \GearmanWorker();
        $settings = $this->getSettings();
        $job = $worker['job'];
        
        if (is_array($job['servers'])) {
            
            foreach ($job['servers'] as $server) {
                list($addr, $port) = explode(':', $server,2);
                $gmworker->addServer($addr, $port);
            }
        } else {
            $gmworker->addServer();
        }
        $gmworker->addFunction($job['realCallableName'], array(new $worker['className'], $job['methodName']));
        
        $iter = isset($job['iter']) ? (int)($job['iter']) : 0;
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
