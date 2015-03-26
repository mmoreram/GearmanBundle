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

namespace Mmoreram\GearmanBundle\Service;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Mmoreram\GearmanBundle\Command\Util\GearmanOutputAwareInterface;
use Mmoreram\GearmanBundle\Event\GearmanWorkExecutedEvent;
use Mmoreram\GearmanBundle\Event\GearmanWorkStartingEvent;
use Mmoreram\GearmanBundle\GearmanEvents;
use Mmoreram\GearmanBundle\Service\Abstracts\AbstractGearmanService;
use Mmoreram\GearmanBundle\Exceptions\ServerConnectionException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Gearman execute methods. All Worker methods
 *
 * @since 2.3.1
 */
class GearmanExecute extends AbstractGearmanService
{
    /**
     * @var ContainerInterface
     *
     * Container instance
     */
    private $container;

    /**
     * @var EventDispatcherInterface
     *
     * EventDispatcher instance
     */
    protected $eventDispatcher;

    /**
     * @var OutputInterface
     *
     * Output instance
     */
    protected $output;

    /**
     * @var OptionsResolver
     */
    protected $executeOptionsResolver;

    /**
     * Construct method
     *
     * @param GearmanCacheWrapper $gearmanCacheWrapper GearmanCacheWrapper
     * @param array               $defaultSettings     The default settings for the bundle
     */
    public function __construct(GearmanCacheWrapper $gearmanCacheWrapper, array $defaultSettings)
    {
        parent::__construct($gearmanCacheWrapper, $defaultSettings);

        $this->executeOptionsResolver = new OptionsResolver();
        $this->executeOptionsResolver
            ->setDefaults(array(
                'iterations'             => null,
                'minimum_execution_time' => null,
                'timeout'                => null
            ))
            ->setAllowedTypes(array(
                'iterations'             => array('null', 'scalar'),
                'minimum_execution_time' => array('null', 'scalar'),
                'timeout'                => array('null', 'scalar')
            ))
        ;
    }

    /**
     * Set container
     *
     * @param ContainerInterface $container Container
     *
     * @return GearmanExecute self Object
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Set event dispatcher
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return GearmanExecute self Object
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Set output
     *
     * @param OutputInterface $output
     *
     * @return GearmanExecute self Object
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Executes a job given a jobName and given settings and annotations of job
     *
     * @param string $jobName Name of job to be executed
     * @param array $options Array of options passed to the callback
     * @param GearmanWorker $gearmanWorker Worker instance to use
     */
    public function executeJob($jobName, array $options = array(), \GearmanWorker $gearmanWorker = null)
    {
        $worker = $this->getJob($jobName);

        if (false !== $worker) {
            $this->callJob($worker, $options, $gearmanWorker);
        }
    }

    /**
     * Given a worker, execute GearmanWorker function defined by job.
     *
     * @param array $worker Worker definition
     * @param array $options Array of options passed to the callback
     * @param GearmanWorker $gearmanWorker Worker instance to use
     * @return GearmanExecute self Object
     */
    private function callJob(Array $worker, array $options = array(), \GearmanWorker $gearmanWorker = null)
    {
        if(is_null($gearmanWorker)){
            $gearmanWorker = new \GearmanWorker;
        }
        $minimumExecutionTime = null;

        if (isset($worker['job'])) {

            $jobs = array($worker['job']);
            $iterations = $worker['job']['iterations'];
            $minimumExecutionTime = $worker['job']['minimumExecutionTime'];
            $timeout = $worker['job']['timeout'];
            $this->addServers($gearmanWorker, $worker['job']['servers'], $successes);

        } else {

            $jobs = $worker['jobs'];
            $iterations = $worker['iterations'];
            $minimumExecutionTime = $worker['minimumExecutionTime'];
            $timeout = $worker['timeout'];
            $this->addServers($gearmanWorker, $worker['servers'], $successes);
        }

        $options = $this->executeOptionsResolver->resolve($options);

        $iterations           = $options['iterations']             ?: $iterations;
        $minimumExecutionTime = $options['minimum_execution_time'] ?: $minimumExecutionTime;
        $timeout              = $options['timeout']                ?: $timeout;

        if (count($successes) < 1) {
            sleep($minimumExecutionTime);
            throw new ServerConnectionException('Worker was unable to connect to any server.');
        }

        $objInstance = $this->createJob($worker);

        /**
         * Start the timer before running the worker.
         */
        $time = time();
        $this->runJob($gearmanWorker, $objInstance, $jobs, $iterations, $timeout);

        /**
         * If there is a minimum expected duration, wait out the remaining period if there is any.
         */
        if (null !== $minimumExecutionTime) {
            $now = time();
            $remaining = $minimumExecutionTime - ($now - $time);

            if ($remaining > 0) {
                sleep($remaining);
            }
        }

        return $this;
    }

    /**
     * Given a worker settings, return Job instance
     *
     * @param array $worker Worker settings
     *
     * @return Object Job instance
     */
    private function createJob(array $worker)
    {
        /**
         * If service is defined, we must retrieve this class with dependency injection
         *
         * Otherwise we just create it with a simple new()
         */
        if ($worker['service']) {

            $objInstance = $this->container->get($worker['service']);

        } else {

            $objInstance = new $worker['className'];

            /**
             * If instance of given object is instanceof
             * ContainerAwareInterface, we inject full container by calling
             * container setter.
             *
             * @see https://github.com/mmoreram/gearman-bundle/pull/12
             */
            if ($objInstance instanceof ContainerAwareInterface) {

                $objInstance->setContainer($this->container);
            }
        }

        return $objInstance;
    }

    /**
     * Given a GearmanWorker and an instance of Job, run it
     *
     * @param \GearmanWorker $gearmanWorker Gearman Worker
     * @param Object         $objInstance   Job instance
     * @param array          $jobs          Array of jobs to subscribe
     * @param integer        $iterations    Number of iterations
     *
     * @return GearmanExecute self Object
     */
    private function runJob(\GearmanWorker $gearmanWorker, $objInstance, array $jobs, $iterations, $timeout = null)
    {
        /**
         * Set the output of this instance, this should allow workers to use the console output.
         */
        if ($objInstance instanceof GearmanOutputAwareInterface) {
            $objInstance->setOutput($this->output ? : new NullOutput());
        }

        /**
         * Every job defined in worker is added into GearmanWorker
         */
        foreach ($jobs as $job) {

            $gearmanWorker->addFunction(
                $job['realCallableName'],
                array($this, 'handleJob'),
                array(
                    'job_object_instance' => $objInstance,
                    'job_method' => $job['methodName'],
                    'jobs' => $jobs
                )
            );
        }

        /**
         * If iterations value is 0, is like worker will never die
         */
        $alive = (0 == $iterations);

        if (null !== $timeout) {
            $gearmanWorker->setTimeout($timeout * 1000);
        }

        /**
         * Executes GearmanWorker with all jobs defined
         */
        while ($gearmanWorker->work()) {

            $iterations--;

            $event = new GearmanWorkExecutedEvent($jobs, $iterations, $gearmanWorker->returnCode());
            $this->eventDispatcher->dispatch(GearmanEvents::GEARMAN_WORK_EXECUTED, $event);

            if ($gearmanWorker->returnCode() != GEARMAN_SUCCESS) {

                break;
            }

            /**
             * Only finishes its execution if alive is false and iterations
             * arrives to 0
             */
            if (!$alive && $iterations <= 0) {

                break;
            }
        }
    }

    /**
     * Adds into worker all defined Servers.
     * If any is defined, performs default method
     *
     * @param \GearmanWorker $gmworker Worker to perform configuration
     * @param array          $servers  Servers array
     *
     * @throws ServerConnectionException if a connection to a server was not possible.
     */
    private function addServers(\GearmanWorker $gmworker, Array $servers, array &$successes = null)
    {
        $successes = $successes ?: null;

        if (!empty($servers)) {

            foreach ($servers as $server) {
                if (@$gmworker->addServer($server['host'], $server['port'])) {
                    $successes[] = $server;
                }
            }
        } else {
            if (@$gmworker->addServer()) {
                $successes[] = array('127.0.0.1', 4730);
            }
        }

        return $successes;
    }

    /**
     * Executes a worker given a workerName subscribing all his jobs inside and
     * given settings and annotations of worker and jobs
     *
     * @param string $workerName Name of worker to be executed
     */
    public function executeWorker($workerName, array $options = array())
    {
        $worker = $this->getWorker($workerName);

        if (false !== $worker) {

            $this->callJob($worker, $options);
        }
    }

    /**
     * Wrapper function handler for all registered functions
     * This allows us to do some nice logging when jobs are started/finished
     *
     * @see https://github.com/brianlmoon/GearmanManager/blob/ffc828dac2547aff76cb4962bb3fcc4f454ec8a2/GearmanPeclManager.php#L95-206
     *
     * @param \GearmanJob $job
     * @param mixed $context
     *
     * @return mixed
     */
    public function handleJob(\GearmanJob $job, $context)
    {
        if (
            !is_array($context)
            || !array_key_exists('job_object_instance', $context)
            || !array_key_exists('job_method', $context)
        ) {
            throw new \InvalidArgumentException('$context shall be an array with job_object_instance and job_method key.');
        }

        $event = new GearmanWorkStartingEvent($context['jobs']);
        $this->eventDispatcher->dispatch(GearmanEvents::GEARMAN_WORK_STARTING, $event);

        $result = call_user_func_array(
            array($context['job_object_instance'], $context['job_method']),
            array($job, $context)
        );

        /**
         * Workaround for PECL bug #17114
         * http://pecl.php.net/bugs/bug.php?id=17114
         */
        $type = gettype($result);
        settype($result, $type);

        return $result;
    }
}
