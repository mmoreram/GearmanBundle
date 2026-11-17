<?php

/**
 * Runs IntegrationWorker in a separate process through the
 * gearman:worker:execute console command, so a test can send blocking jobs
 * (doNormal, runTasks) and wait for their result.
 *
 * Usage: php worker.php <idle-timeout-seconds>
 *
 * The worker processes every queued job and exits once it has been idle for
 * the given timeout. The process exit code is the command exit code.
 */

use Mmoreram\GearmanBundle\Tests\Integration\IntegrationKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

require __DIR__ . '/../../../vendor/autoload.php';

$application = new Application(new IntegrationKernel('test', false));
$application->setAutoExit(false);
$application->setCatchExceptions(true);

exit($application->run(
    new ArrayInput([
        'command' => 'gearman:worker:execute',
        'worker' => 'IntegrationWorker',
        '--timeout' => (int) ($argv[1] ?? 5),
        '--no-interaction' => true,
        '--quiet' => true,
    ]),
    new ConsoleOutput()
));
