<?php

namespace Mmoreram\GearmanBundle\Event\Abstracts;

use GearmanTask;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractGearmanClientTaskEvent extends Event
{
    /**
     * @var GearmanTask
     *
     * Gearman task object
     */
    protected $gearmanTask;

    /**
     * @var mixed
     *
     * Context that can be set in the addTask method
     */
    protected $context;

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

    /**
     * @param mixed $context Context that can be set in the addTask method
     */
    public function setContext($context)
    {
        $this->context = &$context['context'];
    }

    /**
     * @return mixed Context that can be set in the addTask method
     */
    public function &getContext()
    {
        return $this->context;
    }
}
