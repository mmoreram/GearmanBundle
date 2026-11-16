<?php

/**
 * Runs IntegrationWorker in a separate process, so a test can send blocking
 * jobs (doNormal, runTasks) and wait for their result.
 *
 * Usage: php worker.php <idle-timeout-seconds>
 *
 * The worker processes every queued job and exits once it has been idle for
 * the given timeout.
 */

use Mmoreram\GearmanBundle\Tests\Integration\IntegrationKernel;

require __DIR__ . '/../../../vendor/autoload.php';

$kernel = new IntegrationKernel('test', false);
$kernel->boot();

$kernel
    ->getContainer()
    ->get('gearman.execute')
    ->executeWorker('IntegrationWorker', [
        'timeout' => (int) ($argv[1] ?? 5),
    ]);
