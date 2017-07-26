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
 * Event object sent to gearman.worker.job.return kernel event listeners. Provides information about return value sent
 * by job to Gearman Job Server
 * @package Mmoreram\GearmanBundle\Event\Worker
 * @since 3.1.0
 */
class JobReturnEvent extends AbstractGearmanJobEvent
{
    /**
     * Gearman return value to be sent to client
     * @var string
     */
    protected $returnValue;

    /**
     * Getter for gearman return value
     * @return string
     */
    public function getReturnValue()
    {
        return $this->returnValue;
    }

    /**
     * Setter for gearman return value
     * @param string $returnValue
     * @return $this
     */
    public function setReturnValue($returnValue)
    {
        $this->returnValue = $returnValue;
        return $this;
    }

}
