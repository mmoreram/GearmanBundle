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

use Symfony\Component\EventDispatcher\Event as BaseEventDeprecated;
use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

// Symfony 4.3 BC layer
if (class_exists(BaseEvent::class)) {
    /**
     * @internal
     */
    abstract class Event extends BaseEvent
    {
    }
} else {
    /**
     * @internal
     */
    abstract class Event extends BaseEventDeprecated
    {
    }
}
