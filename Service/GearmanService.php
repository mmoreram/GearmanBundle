<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Mmoreramerino\GearmanBundle\Module\GearmanCache as Cache;
use Mmoreramerino\GearmanBundle\Exceptions\JobDoesNotExistException;

/**
 * Gearman execute methods. All Worker methods
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
class GearmanService extends GearmanSettings
{
    
    /**
     * All workers
     *
     * @var type 
     */
    protected $workers = null;
    
    /**
     * All settings
     * 
     * @var settings
     */
    protected $settings = null;
    
    /**
     * Retrieve all Workers from cache
     * Return $workers
     *
     * @return Array 
     */
    function setWorkers()
    {
        if (!is_array($this->workers)) {
            
            $rootDir = $this->container->get('kernel')->getRootDir();
            $this->cachedir = $rootDir . '/cache/'.$this->container->get('kernel')->getEnvironment().'/Gearman/';

            $gearmanCache = new Cache($this->cachedir);
            $this->workers = $gearmanCache->get();
        }
        
        /**
         * Always will be an Array
         */
        return $this->workers;
    }
    
    /**
     * Return worker containing a job with $jobName as name
     * If is not found, throws JobDoesNotExistException Exception
     *
     * @param string $jobName
     * @return Array 
     */
    function getWorker($jobName)
    {
        $this->setWorkers();
        
        foreach ($this->workers as $worker) {
            if (is_array($worker['jobs'])) {
                foreach ($worker['jobs'] as $job) {
                    if($jobName === $job['realCallableName']) {
                        $worker['job'] = $job;
                        return $worker;
                    }
                }
            }
        }
        
        throw new JobDoesNotExistException($jobName);
    }
    
    /**
     * Set gearman settings
     *
     * @param array $settings 
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }
}
