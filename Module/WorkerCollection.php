<?php

namespace Ulabox\GearmanBundle\Module;

use Ulabox\GearmanBundle\Module\WorkerClass as Worker;

/**
 * WorkerCollection class
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
class WorkerCollection
{
    
    /**
     * All Workers
     *
     * @var array
     */
    private $workerClasses = array();
    
    /**
     * Adds a Worker into $workerClasses
     * Return self object
     *
     * @param Worker $workerClass
     * @return WorkerCollection 
     */
    public function add(Worker $workerClass)
    {
        $this->workerClasses[] = $workerClass;
        return $this;
    }
    
    /**
     * Retrieve all workers loaded previously in cache format
     *
     * @return array 
     */
    public function __toCache()
    {
        $workersDumped = array();
        foreach ($this->workerClasses as $worker) {
            $workersDumped[] = $worker->__toCache();
        }
        return $workersDumped;
    }
}
