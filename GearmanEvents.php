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

namespace Mmoreram\GearmanBundle;

/**
 * Events dispatched by GearmanBundle
 *
 * @since 2.3.1
 */
class GearmanEvents
{
    /**
     * Sets the callback function to be used when a task is completed
     *
     * event.name : gearman.client.callback.complete
     * event.class : GearmanClientCallbackCompleteEvent
     *
     * @var string
     */
    const GEARMAN_CLIENT_CALLBACK_COMPLETE = 'gearman.client.callback.complete';

    /**
     * Sets the callback function to be used when a task does not complete
     * successfully
     *
     * event.name : gearman.client.callback.fail
     * event.class : GearmanClientCallbackFailEvent
     *
     * @var string
     */
    const GEARMAN_CLIENT_CALLBACK_FAIL = 'gearman.client.callback.fail';

    /**
     * Sets the callback function for accepting data packets for a task
     *
     * event.name : gearman.client.callback.data
     * event.class : GearmanClientCallbackDataEvent
     *
     * @var string
     */
    const GEARMAN_CLIENT_CALLBACK_DATA = 'gearman.client.callback.data';

    /**
     * Sets a function to be called when a task is received and queued by the
     * Gearman job server
     *
     * event.name : gearman.client.callback.created
     * event.class : GearmanClientCallbackCreatedEvent
     *
     * @var string
     */
    const GEARMAN_CLIENT_CALLBACK_CREATED = 'gearman.client.callback.created';

    /**
     * Specifies a function to call when a worker for a task sends an exception
     *
     * event.name : gearman.client.callback.exception
     * event.class : GearmanClientCallbackExceptionEvent
     *
     * @var string
     */
    const GEARMAN_CLIENT_CALLBACK_EXCEPTION = 'gearman.client.callback.exception';

    /**
     * Sets a callback function used for getting updated status information from
     * a worker
     *
     * event.name : gearman.client.callback.status
     * event.class : GearmanClientCallbackStatusEvent
     *
     * @var string
     */
    const GEARMAN_CLIENT_CALLBACK_STATUS = 'gearman.client.callback.status';

    /**
     * Sets a function to be called when a worker sends a warning
     *
     * event.name : gearman.client.callback.warning
     * event.class : GearmanClientCallbackWarningEvent
     *
     * @var string
     */
    const GEARMAN_CLIENT_CALLBACK_WARNING = 'gearman.client.callback.warning';

    /**
     * Sets a function to be called when a worker needs to send back data prior
     * to job completion.
     *
     * A worker can do this when it needs to send updates, send partial results,
     * or flush data during long running jobs
     *
     * event.name : gearman.client.callback.workload
     * event.class : GearmanClientCallbackWorkloadEvent
     *
     * @var string
     */
    const GEARMAN_CLIENT_CALLBACK_WORKLOAD = 'gearman.client.callback.workload';

    /**
     * Sets a function to be called when a worker has completed a job.
     *
     * This will be fired by the worker after completion of a job before preparing to start another work cycle.
     *
     * @var string
     */
    const GEARMAN_WORK_EXECUTED = 'gearman.work.executed';

    /**
     * Sets a function to be called when a worker is starting a job.
     *
     * This will be fired when the worker start another work cycle.
     *
     * @var string
     */
    const GEARMAN_WORK_STARTING = 'gearman.work.starting';
}
