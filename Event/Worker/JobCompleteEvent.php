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
 * Event object class sent to gearman.worker.job.complete kernel event listeners. Provides information about
 * serialized return data sent to Gearman Job Server and client by job
 * @package Mmoreram\GearmanBundle\Event\Worker
 * @since 3.1.0
 */
class JobCompleteEvent extends AbstractGearmanJobEvent
{
    /**
     * Serialized result data to be sent to client
     * @var string
     */
    protected $result;

    /**
     * Getter of serialized result
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Setter of serialized result
     * @param string $result
     * @return JobCompleteEvent $this
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

}
