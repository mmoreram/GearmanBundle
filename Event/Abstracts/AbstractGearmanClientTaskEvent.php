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

namespace Mmoreram\GearmanBundle\Event\Abstracts;

use GearmanTask;
use Symfony\Component\EventDispatcher\Event;

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
