<?php

/**
 * Gearman Bundle for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Janusz Paszyński <jpaszynski@have2code.com>
 */

namespace Mmoreram\GearmanBundle\Event\Worker;


use Mmoreram\GearmanBundle\Event\Abstracts\AbstractGearmanJobEvent;


/**
 * Event object sent to gearman.worker.job.data kernel event listeners. Provides information about serialized data sent
 * to Gearman Job Server and client by job
 * @package Mmoreram\GearmanBundle\Event\Worker
 * @since 3.1.0
 */
class JobDataEvent extends AbstractGearmanJobEvent
{
    /**
     * Data that is set by job to be send to client
     * @var string
     */
    protected $data;

    /**
     * Getter for serialized data
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Setter for serialized data
     * @param string $data
     * @return JobDataEvent $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

}
