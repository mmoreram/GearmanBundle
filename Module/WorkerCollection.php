<?php

/**
 * Gearman Bundle for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Mmoreram\GearmanBundle\Module;

use Mmoreram\GearmanBundle\Module\WorkerClass as Worker;

/**
 * WorkerCollection class
 *
 * @since 2.3.1
 */
class WorkerCollection
{
    /**
     * All Workers
     *
     * @var array
     */
    private $workerClasses = [];

    /**
     * Adds a Worker into $workerClasses
     * Return self object
     *
     * @param Worker $workerClass Worker element to add
     *
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
    public function toArray()
    {
        $workersDumped = [];

        foreach ($this->workerClasses as $worker) {
            $workersDumped[] = $worker->toArray();
        }

        return $workersDumped;
    }
}
