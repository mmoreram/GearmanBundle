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
 * Event object sent to each gearman.worker.job.warning kernel event listener. Provides information about message sent
 * to Gearman Job Server
 * @package Mmoreram\GearmanBundle\Event\Worker
 */
class JobWarningEvent extends AbstractGearmanJobEvent
{
    /**
     * Warning message to be sent to client
     * @var string
     */
    protected $message;

    /**
     * Getter for message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Setter for message
     * @param string $message
     * @return JobWarningEvent $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

}
