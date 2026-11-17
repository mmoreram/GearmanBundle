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

namespace Mmoreram\GearmanBundle\Tests\Integration\Fixtures\Worker;

use Mmoreram\GearmanBundle\Driver\Gearman;

/**
 * Worker used by the integration tests against a real gearmand server.
 *
 * iterations=0 keeps the worker alive until it is idle for the timeout passed
 * when it is executed, so it drains every queued job and then exits.
 *
 * @Gearman\Work(
 *     name="IntegrationWorker",
 *     description="Worker used by the integration tests",
 *     iterations=0,
 *     defaultMethod="doNormal"
 * )
 */
class IntegrationWorker
{
    /**
     * Returns the workload reversed, to check results travel back to the client.
     *
     * @Gearman\Job(name="reverse", description="Reverse the workload")
     */
    public function reverse(\GearmanJob $job): string
    {
        return strrev($job->workload());
    }

    /**
     * Writes the token of a JSON workload {"file": ..., "token": ...} into
     * the given file, to check background jobs are executed.
     *
     * Malformed workloads (e.g. a job queued by hand) are dropped, so a stray
     * job left in gearmand cannot crash the worker of a later test run.
     *
     * @Gearman\Job(name="record", description="Record the workload token in a file")
     */
    public function record(\GearmanJob $job): bool
    {
        $workload = json_decode($job->workload(), true);

        if (!is_array($workload) || empty($workload['file']) || !isset($workload['token'])) {
            return false;
        }

        file_put_contents($workload['file'], $workload['token'] . PHP_EOL, FILE_APPEND);

        return true;
    }
}
