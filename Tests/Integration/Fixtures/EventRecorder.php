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

namespace Mmoreram\GearmanBundle\Tests\Integration\Fixtures;

use Mmoreram\GearmanBundle\Event\GearmanWorkExecutedEvent;
use Mmoreram\GearmanBundle\Event\GearmanWorkStartingEvent;
use Mmoreram\GearmanBundle\GearmanEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Records the worker events dispatched in a worker process, one JSON line per
 * event, into the file named by the GEARMAN_INTEGRATION_EVENT_LOG environment
 * variable, so the test process can assert on them.
 */
class EventRecorder implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            GearmanEvents::GEARMAN_WORK_STARTING => 'onWorkStarting',
            GearmanEvents::GEARMAN_WORK_EXECUTED => 'onWorkExecuted',
        ];
    }

    public function onWorkStarting(GearmanWorkStartingEvent $event): void
    {
        $this->record([
            'event' => GearmanEvents::GEARMAN_WORK_STARTING,
            'jobs' => array_column($event->getJobs(), 'realCallableName'),
        ]);
    }

    public function onWorkExecuted(GearmanWorkExecutedEvent $event): void
    {
        $this->record([
            'event' => GearmanEvents::GEARMAN_WORK_EXECUTED,
            'jobs' => array_column($event->getJobs(), 'realCallableName'),
            'returnCode' => $event->getReturnCode(),
        ]);
    }

    private function record(array $entry): void
    {
        $file = getenv('GEARMAN_INTEGRATION_EVENT_LOG');

        if ($file) {
            file_put_contents($file, json_encode($entry) . PHP_EOL, FILE_APPEND);
        }
    }
}
