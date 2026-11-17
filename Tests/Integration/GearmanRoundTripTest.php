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
use Mmoreram\GearmanBundle\GearmanEvents;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Sends jobs through the bundle client to a real gearmand server and processes
 * them by running the gearman:worker:execute console command in a separate
 * process (see bin/worker.php).
 *
 * Requires a gearmand server listening on 127.0.0.1:4730, otherwise the tests
 * are skipped (or fail when GEARMAN_INTEGRATION_REQUIRED is set, as in CI).
 * Start one with e.g. `gearmand -d`. Run only these tests with
 * `vendor/bin/phpunit --group integration`.
 *
 * Every test uses unique payloads and asserts on its own results only, so jobs
 * left in the queue by an earlier interrupted run do not affect the outcome:
 * workers drain the whole queue and exit once idle. A worker that does not
 * exit in time is killed, so the tests never hang.
 *
 * @group integration
 */
class GearmanRoundTripTest extends TestCase
{
    private const HOST = '127.0.0.1';
    private const PORT = 4730;

    /** Seconds a worker waits for a new job before exiting. */
    private const WORKER_IDLE_TIMEOUT = 2;

    /** Seconds a worker process may take, beyond its idle timeout, to boot and exit. */
    private const WORKER_EXIT_TIMEOUT = 15;

    /** Milliseconds a client waits for a result before giving up. */
    private const CLIENT_TIMEOUT = 20000;

    private IntegrationKernel $kernel;

    /** @var array<int, array{string, callable}> */
    private array $listeners = [];

    /** @var resource[] */
    private array $workerProcesses = [];

    /** @var string[] */
    private array $tempFiles = [];

    private string $eventLog;

    /** Kernel cache dir of this run, shared with the worker processes. */
    private static ?string $cacheDir = null;

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

        // Fresh kernel cache per run: workers boot without debug, so they would otherwise reuse a container
        // compiled from an older configuration and hide configuration regressions
        self::$cacheDir = sys_get_temp_dir() . '/gearman-bundle-integration-' . bin2hex(random_bytes(8));
        putenv('GEARMAN_INTEGRATION_CACHE_DIR=' . self::$cacheDir);
    }

    public static function tearDownAfterClass(): void
    {
        if (null === self::$cacheDir) {
            return;
        }

        putenv('GEARMAN_INTEGRATION_CACHE_DIR');

        if (is_dir(self::$cacheDir)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(self::$cacheDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
            }
            rmdir(self::$cacheDir);
        }

        self::$cacheDir = null;
    }

    protected function setUp(): void
    {
        $this->kernel = new IntegrationKernel('test', true);
        $this->kernel->boot();
        $this->eventLog = $this->createTempFile();
    }

    protected function tearDown(): void
    {
        $dispatcher = $this->getEventDispatcher();
        foreach ($this->listeners as [$eventName, $listener]) {
            $dispatcher->removeListener($eventName, $listener);
        }

        // Only reached for processes still running, e.g. when an assertion failed before waitForWorkerProcesses().
        // SIGKILL, because the command handles SIGTERM by finishing the current work() call, which may block.
        foreach ($this->workerProcesses as $process) {
            proc_terminate($process, 9);
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

        $this->startWorkerProcess();
        $this->waitForWorkerProcesses();

        $this->assertContains($token, file($file, FILE_IGNORE_NEW_LINES));
    }

    /**
     * A malformed job left in the queue (e.g. queued by hand or by an
     * interrupted run) is dropped by the worker, which still processes the
     * next job and exits cleanly.
     */
    public function testMalformedLeftoverJobDoesNotBreakWorker(): void
    {
        $file = $this->createTempFile();
        $token = uniqid('after-malformed-', true);

        $this->getClient()->doBackgroundJob('IntegrationWorker~record', 'not json', uniqid('malformed-', true));
        $this->getClient()->doBackgroundJob(
            'IntegrationWorker~record',
            json_encode(['file' => $file, 'token' => $token])
        );

        $this->startWorkerProcess();
        $this->waitForWorkerProcesses();

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
        $result = $client->doNormalJob('IntegrationWorker~reverse', $token);

        $this->assertSame(GEARMAN_SUCCESS, $client->getReturnCode());
        $this->assertSame(strrev($token), $result);

        $this->waitForWorkerProcesses();
    }

    /**
     * The worker dispatches gearman.work.starting before running a job and
     * gearman.work.executed after it, to subscribers registered in the
     * container (see Fixtures\EventRecorder).
     */
    public function testWorkerDispatchesStartingAndExecutedEvents(): void
    {
        $file = $this->createTempFile();
        $token = uniqid('events-', true);

        $this->getClient()->doBackgroundJob(
            'IntegrationWorker~record',
            json_encode(['file' => $file, 'token' => $token])
        );

        $this->startWorkerProcess();
        $this->waitForWorkerProcesses();

        $this->assertContains($token, file($file, FILE_IGNORE_NEW_LINES));

        $events = array_map(
            function (string $line) {
                return json_decode($line, true);
            },
            file($this->eventLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        );
        $starting = array_values(array_filter($events, function (array $event) {
            return GearmanEvents::GEARMAN_WORK_STARTING === $event['event'];
        }));
        $executed = array_values(array_filter($events, function (array $event) {
            return GearmanEvents::GEARMAN_WORK_EXECUTED === $event['event'];
        }));

        $this->assertNotEmpty($starting, 'gearman.work.starting was not dispatched');
        $this->assertNotEmpty($executed, 'gearman.work.executed was not dispatched');
        $this->assertCount(count($starting), $executed);

        // Both events carry every job the worker registered, not only the one that ran
        foreach (array_merge($starting, $executed) as $event) {
            $this->assertEqualsCanonicalizing(['IntegrationWorker~record', 'IntegrationWorker~reverse'], $event['jobs']);
        }

        foreach ($executed as $event) {
            $this->assertSame(GEARMAN_SUCCESS, $event['returnCode']);
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
        foreach ($tokens as $token) {
            $client->addTask('IntegrationWorker~reverse', $token);
        }

        $this->assertTrue($client->runTasks());

        sort($results);
        $expected = array_map('strrev', $tokens);
        sort($expected);
        $this->assertSame($expected, $results);

        $this->waitForWorkerProcesses();
    }

    /**
     * Returns the bundle client with a bounded wait on its native client, so
     * a server that accepts connections but stops responding fails the test
     * instead of hanging it.
     *
     * The bundle drops its native client after each job call or runTasks(),
     * so call this right before every client call.
     */
    private function getClient(): GearmanClient
    {
        /** @var GearmanClient $client */
        $client = $this->kernel->getContainer()->get('gearman');
        $client->getNativeClient()->setTimeout(self::CLIENT_TIMEOUT);

        return $client;
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
     * Starts the gearman:worker:execute command in a separate process. It
     * drains the queue and exits once idle, see waitForWorkerProcesses().
     */
    private function startWorkerProcess(): void
    {
        $process = proc_open(
            [PHP_BINARY, __DIR__ . '/bin/worker.php', (string) self::WORKER_IDLE_TIMEOUT],
            [1 => ['file', '/dev/null', 'w'], 2 => STDERR],
            $pipes,
            null,
            array_merge(getenv(), ['GEARMAN_INTEGRATION_EVENT_LOG' => $this->eventLog])
        );

        $this->assertIsResource($process, 'Could not start the worker process');
        $this->workerProcesses[] = $process;
    }

    /**
     * Waits for the worker processes to exit on their own once idle and
     * asserts the command succeeded. A process still running after the
     * deadline makes the test fail and is killed in tearDown().
     */
    private function waitForWorkerProcesses(): void
    {
        // Monotonic clock: a wall-clock jump (NTP sync, VM resume) must not shorten or stretch the wait
        $deadline = hrtime(true) + (self::WORKER_IDLE_TIMEOUT + self::WORKER_EXIT_TIMEOUT) * 1000000000;

        foreach ($this->workerProcesses as $key => $process) {
            while (($status = proc_get_status($process))['running'] && hrtime(true) < $deadline) {
                usleep(100000);
            }

            $this->assertFalse($status['running'], 'The worker process did not exit once idle');

            proc_close($process);
            unset($this->workerProcesses[$key]);

            $this->assertSame(0, $status['exitcode'], 'The worker process failed');
        }
    }

    private function createTempFile(): string
    {
        $file = tempnam(sys_get_temp_dir(), 'gearman-bundle-');
        $this->tempFiles[] = $file;

        return $file;
    }
}
