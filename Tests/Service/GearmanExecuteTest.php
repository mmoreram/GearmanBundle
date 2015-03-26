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

namespace Mmoreram\GearmanBundle\Tests\Service;

use Mmoreram\GearmanBundle\Service\GearmanExecute;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Mmoreram\GearmanBundle\GearmanEvents;

/**
 * Tests GearmanExecute class
 */
class GearmanExecuteTest extends WebTestCase
{

    /**
     * Test service can be instanced through container
     */
    public function testGearmanExecuteLoadFromContainer()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->assertInstanceOf(
            '\Mmoreram\GearmanBundle\Service\GearmanExecute',
            static::$kernel
                ->getContainer()
                ->get('gearman.execute')
        );
    }

    public function testDispatchingEventsOnJob()
    {
        // Worker mock
        $worker = $this->getMockBuilder('\GearmanWorker')
            ->disableOriginalConstructor()
            ->getMock();
        $worker->method('addServer')->willReturn(true);

        // Wrapper mock
        $workers = array(
            0 => array(
                'className'    => "Mmoreram\\GearmanBundle\\Tests\\Service\\Mocks\\SingleCleanFile",
                'fileName'     => dirname(__FILE__) . '/Mocks/SingleCleanFile.php',
                'callableName' => null,
                'description'  => "test",
                'service'      => false,
                'servers'      => array(),
                'iterations'   => 1,
                'timeout'      => null,
                'minimumExecutionTime' => null,
                'jobs' => array(
                    0 => array(
                        'callableName'             => "test",
                        'methodName'               => "test",
                        'realCallableName'         => "test",
                        'jobPrefix'                => NULL,
                        'realCallableNameNoPrefix' => "test",
                        'description'              => "test",
                        'iterations'               => 1,
                        'servers'                  => array(),
                        'defaultMethod'            => "doBackground",
                        'minimumExecutionTime'     => null,
                        'timeout'                  => null,
                    )
                )
            )
        );
        $wrapper = $this->getMockBuilder('Mmoreram\GearmanBundle\Service\GearmanCacheWrapper')
            ->disableOriginalConstructor()
            ->getMock();
        $wrapper->method('getWorkers')
            ->willReturn($workers);

        // Prepare a dispatcher to listen to tested events
        $startingFlag = false;
        $executedFlag = false;

        $dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
        $dispatcher->addListener(GearmanEvents::GEARMAN_WORK_STARTING, function() use (&$startingFlag){
            $startingFlag = true;
        });
        $dispatcher->addListener(GearmanEvents::GEARMAN_WORK_EXECUTED, function() use (&$executedFlag){
            $executedFlag = true;
        });

        // Create the service under test
        $service = new GearmanExecute($wrapper, array());
        $service->setEventDispatcher($dispatcher);

        // We need a job object, this part could be improved
        $object = new \Mmoreram\GearmanBundle\Tests\Service\Mocks\SingleCleanFile();

        // Finalize worker mock by making it call our job object
        // This is normally handled by Gearman, but for test purpose we must simulate it
        $worker->method('work')->will($this->returnCallback(function() use ($service, $object){
            $service->handleJob(new \GearmanJob(), array(
                'job_object_instance' => $object,
                'job_method' => 'myMethod',
                'jobs' => array()
            ));
            return true;
        }));

        // Execute a job :)
        $service->executeJob('test', array(), $worker);

        // Do we have the events ?
        $this->assertTrue($startingFlag);
        $this->assertTrue($executedFlag);
    }
}
