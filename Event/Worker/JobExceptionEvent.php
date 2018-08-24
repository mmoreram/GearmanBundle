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
 * Event object sent to gearman.worker.job.exception kernel event listeners. Provide information about exception message
 * sent by job to Gearman Job Server and client.
 * @package Mmoreram\GearmanBundle\Event\Worker
 * @since 3.1.0
 */
class JobExceptionEvent extends AbstractGearmanJobEvent
{
    /**
     * Exception description to be sent to client
     * @var string
     */
    protected $exception;

    /**
     * Getter for exception description
     * @return string
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Setter for exception description
     * @param string $exception
     * @return JobExceptionEvent $this
     */
    public function setException($exception)
    {
        $this->exception = $exception;
        return $this;
    }

}
