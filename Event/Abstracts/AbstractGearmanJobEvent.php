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

namespace Mmoreram\GearmanBundle\Event\Abstracts;


use Symfony\Component\EventDispatcher\Event;

/**
 * Class AbstractGearmanJobEvent
 * @package Mmoreram\GearmanBundle\Event\Abstracts
 * @since 3.1.0
 */
class AbstractGearmanJobEvent extends Event
{
    /**
     * {@see \GearmanJob} instance associated with this event
     * @var \GearmanJob
     */
    protected $job;

    public function __construct(\GearmanJob $job)
    {
        $this->job = $job;
    }

    /**
     * Getter for {@see \GearmanJob} associated with this event
     * @return \GearmanJob object associated with this event
     */
    public function getJob()
    {
        return $this->job;
    }
}
