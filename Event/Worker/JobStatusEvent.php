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
 * Event data sent to gearman.worker.job.status kernel event listeners. Provide information about status of job as sent
 * to Gearman Job Server.
 * @package Mmoreram\GearmanBundle\Event\Worker
 * @since 3.1.0
 */
class JobStatusEvent extends AbstractGearmanJobEvent
{
    /**
     * The numerator of the precentage completed expressed as a fraction.
     * @var int
     */
    protected $numerator;
    /**
     * The denominator of the precentage completed expressed as a fraction.
     * @var int
     */
    protected $denumerator;

    /**
     * Getter for numerator
     * @return int
     */
    public function getNumerator()
    {
        return $this->numerator;
    }

    /**
     * Setter for numerator
     * @param int $numerator The numerator of the precentage completed expressed as a fraction.
     * @return JobStatusEvent $this
     */
    public function setNumerator($numerator)
    {
        $this->numerator = $numerator;
        return $this;
    }

    /**
     * Getter for denumerator
     * @return int
     */
    public function getDenumerator()
    {
        return $this->denumerator;
    }

    /**
     * Setter for denominator
     * @param int $denumerator The denominator of the precentage completed expressed as a fraction.
     * @return JobStatusEvent $this
     */
    public function setDenumerator($denumerator)
    {
        $this->denumerator = $denumerator;
        return $this;
    }

}
