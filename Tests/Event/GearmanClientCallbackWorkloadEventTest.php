<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Event;

use Mmoreram\GearmanBundle\Event\GearmanClientCallbackWorkloadEvent;

/**
 * Tests GearmanClientCallbackWorkloadEventTest class
 */
class GearmanClientCallbackWorkloadEventTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GearmanClientCallbackWorkloadEvent
     *
     * Object to test
     */
    private $gearmanClientCallbackWorkloadEvent;

    /**
     * @var array
     *
     * Payload for testing
     */
    private $gearmanTask;

    /**
     * Setup
     */
    public function setUp()
    {

        $this->gearmanTask = $this->getMock('\GearmanTask');
        $this->gearmanClientCallbackWorkloadEvent = new GearmanClientCallbackWorkloadEvent($this->gearmanTask);
    }

    /**
     * Testing payload getter
     */
    public function testGetGearmanTask()
    {
        $this->assertEquals($this->gearmanClientCallbackWorkloadEvent->getGearmanTask(), $this->gearmanTask);
    }

    /**
     * Tests if Event extends needed classes
    */
    public function testInstancesOf()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->gearmanClientCallbackWorkloadEvent);
        $this->assertInstanceOf('Mmoreram\GearmanBundle\Event\Abstracts\AbstractGearmanClientTaskEvent', $this->gearmanClientCallbackWorkloadEvent);
    }
}
