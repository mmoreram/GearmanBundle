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

namespace Mmoreram\GearmanBundle\Tests\Integration;

use Mmoreram\GearmanBundle\Event\GearmanClientCallbackCompleteEvent;
use Mmoreram\GearmanBundle\Event\GearmanWorkExecutedEvent;
use Mmoreram\GearmanBundle\Event\GearmanWorkStartingEvent;
use Mmoreram\GearmanBundle\GearmanEvents;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use Mmoreram\GearmanBundle\Service\GearmanExecute;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Sends jobs through the bundle client to a real gearmand server and processes
 * them with the bundle worker.
 *
 * Requires a gearmand server listening on 127.0.0.1:4730, otherwise the tests
 * are skipped (or fail when GEARMAN_INTEGRATION_REQUIRED is set, as in CI).
 * Start one with e.g. `gearmand -d`. Run only these tests with
 * `vendor/bin/phpunit --group integration`.
 *
 * Every test uses unique payloads and asserts on its own results only, so jobs
 * left in the queue by an earlier interrupted run do not affect the outcome:
 * workers drain the whole queue and exit once idle.
 *
 * @group integration
 */
class GearmanRoundTripTest extends TestCase
{
    private const HOST = '127.0.0.1';
    private const PORT = 4730;

    /** Seconds a worker waits for a new job before exiting. */
    private const WORKER_IDLE_TIMEOUT = 2;

    /** Milliseconds a client waits for a result before giving up. */
    private const CLIENT_TIMEOUT = 20000;

    private IntegrationKernel $kernel;

    /** @var array<int, array{string, callable}> */
    private array $listeners = [];

    /** @var resource[] */
    private array $workerProcesses = [];

    /** @var string[] */
    private array $tempFiles = [];

    public static function setUpBeforeClass(): void
    {
        $socket = @fsockopen(self::HOST, self::PORT, $errno, $errstr, 1);

        if (false === $socket) {
            $message = sprintf('No gearmand server reachable on %s:%d (%s).', self::HOST, self::PORT, $errstr);

            // CI sets GEARMAN_INTEGRATION_REQUIRED so a missing server fails the build instead of skipping silently
            if (getenv('GEARMAN_INTEGRATION_REQUIRED')) {
                self::fail($message);
            }

            self::markTestSkipped($message);
        }

        fclose($socket);
    }

    protected function setUp(): void
    {
        $this->kernel = new IntegrationKernel('test', true);
        $this->kernel->boot();
    }

    protected function tearDown(): void
    {
        $dispatcher = $this->getEventDispatcher();
        foreach ($this->listeners as [$eventName, $listener]) {
            $dispatcher->removeListener($eventName, $listener);
        }

        foreach ($this->workerProcesses as $process) {
            proc_terminate($process);
            proc_close($process);
        }

        foreach ($this->tempFiles as $file) {
            @unlink($file);
        }

        $this->kernel->shutdown();
    }

    /**
     * A background job is queued by the client and executed by the worker,
     * with its payload intact.
     */
    public function testBackgroundJobIsExecutedByWorker(): void
    {
        $file = $this->createTempFile();
        $token = uniqid('background-', true);

        $client = $this->getClient();
        $handle = $client->doBackgroundJob(
            'IntegrationWorker~record',
            json_encode(['file' => $file, 'token' => $token])
        );

        $this->assertSame(GEARMAN_SUCCESS, $client->getReturnCode());
        $this->assertNotEmpty($handle);

        $this->runWorkerInProcess();

        $this->assertContains($token, file($file, FILE_IGNORE_NEW_LINES));
    }

    /**
     * A normal (blocking) job returns the worker result to the client.
     */
    public function testNormalJobReturnsWorkerResult(): void
    {
        $this->startWorkerProcess();
        $token = uniqid('normal-', true);

        $client = $this->getClient();
        $client->getNativeClient()->setTimeout(self::CLIENT_TIMEOUT);
        $result = $client->doNormalJob('IntegrationWorker~reverse', $token);

        $this->assertSame(GEARMAN_SUCCESS, $client->getReturnCode());
        $this->assertSame(strrev($token), $result);
    }

    /**
     * The worker dispatches gearman.work.starting before running a job and
     * gearman.work.executed after it.
     */
    public function testWorkerDispatchesStartingAndExecutedEvents(): void
    {
        $file = $this->createTempFile();
        $token = uniqid('events-', true);

        $starting = [];
        $executed = [];
        $this->listen(GearmanEvents::GEARMAN_WORK_STARTING, function (GearmanWorkStartingEvent $event) use (&$starting) {
            $starting[] = $event;
        });
        $this->listen(GearmanEvents::GEARMAN_WORK_EXECUTED, function (GearmanWorkExecutedEvent $event) use (&$executed) {
            $executed[] = $event;
        });

        $this->getClient()->doBackgroundJob(
            'IntegrationWorker~record',
            json_encode(['file' => $file, 'token' => $token])
        );

        $this->runWorkerInProcess();

        $this->assertContains($token, file($file, FILE_IGNORE_NEW_LINES));
        $this->assertNotEmpty($starting, 'gearman.work.starting was not dispatched');
        $this->assertNotEmpty($executed, 'gearman.work.executed was not dispatched');
        $this->assertCount(count($starting), $executed);

        $jobNames = array_column($starting[0]->getJobs(), 'realCallableName');
        $this->assertContains('IntegrationWorker~record', $jobNames);

        foreach ($executed as $event) {
            $this->assertSame(GEARMAN_SUCCESS, $event->getReturnCode());
        }
    }

    /**
     * Tasks added to the client run in parallel and every completed task
     * dispatches gearman.client.callback.complete with the worker result.
     */
    public function testTasksDispatchCompleteCallbackWithResults(): void
    {
        $this->startWorkerProcess();
        $tokens = [uniqid('task-a-', true), uniqid('task-b-', true)];

        $results = [];
        $this->listen(GearmanEvents::GEARMAN_CLIENT_CALLBACK_COMPLETE, function (GearmanClientCallbackCompleteEvent $event) use (&$results) {
            $results[] = $event->getGearmanTask()->data();
        });

        $client = $this->getClient();
        $client->getNativeClient()->setTimeout(self::CLIENT_TIMEOUT);
        foreach ($tokens as $token) {
            $client->addTask('IntegrationWorker~reverse', $token);
        }

        $this->assertTrue($client->runTasks());

        sort($results);
        $expected = array_map('strrev', $tokens);
        sort($expected);
        $this->assertSame($expected, $results);
    }

    private function getClient(): GearmanClient
    {
        return $this->kernel->getContainer()->get('gearman');
    }

    private function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->kernel->getContainer()->get('event_dispatcher');
    }

    private function listen(string $eventName, callable $listener): void
    {
        $this->getEventDispatcher()->addListener($eventName, $listener);
        $this->listeners[] = [$eventName, $listener];
    }

    /**
     * Runs the worker in this process until it has been idle for
     * WORKER_IDLE_TIMEOUT seconds.
     */
    private function runWorkerInProcess(): void
    {
        /** @var GearmanExecute $execute */
        $execute = $this->kernel->getContainer()->get('gearman.execute');
        $execute->executeWorker('IntegrationWorker', [
            'timeout' => self::WORKER_IDLE_TIMEOUT,
        ]);
    }

    /**
     * Starts the worker in a separate process, for jobs the client waits on.
     * It exits once idle; tearDown() terminates it if it is still running.
     */
    private function startWorkerProcess(): void
    {
        $process = proc_open(
            [PHP_BINARY, __DIR__ . '/bin/worker.php', (string) self::WORKER_IDLE_TIMEOUT],
            [1 => ['file', '/dev/null', 'w'], 2 => STDERR],
            $pipes
        );

        $this->assertIsResource($process, 'Could not start the worker process');
        $this->workerProcesses[] = $process;
    }

    private function createTempFile(): string
    {
        $file = tempnam(sys_get_temp_dir(), 'gearman-bundle-');
        $this->tempFiles[] = $file;

        return $file;
    }
}
