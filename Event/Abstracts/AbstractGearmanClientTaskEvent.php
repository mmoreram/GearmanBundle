<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since  2013
 */

namespace Mmoreram\GearmanBundle\Event\Abstracts;

use Symfony\Component\EventDispatcher\Event;
use GearmanTask;

/**
 * AbstractGearmanClientTaskEvent
 */
abstract class AbstractGearmanClientTaskEvent extends Event
{
    /**
     * @var GearmanTask
     *
     * Gearman task object
     */
    protected $gearmanTask;

    /**
     * Construct method
     *
     * @param GearmanTask $gearmanTask Gearman Task
     */
    public function __construct(GearmanTask $gearmanTask)
    {
        $this->gearmanTask = $gearmanTask;
    }

    /**
     * Get Gearman Task
     *
     * @return GearmanTask Gearman Task
     */
    public function getGearmanTask()
    {
        return $this->gearmanTask;
    }
}
