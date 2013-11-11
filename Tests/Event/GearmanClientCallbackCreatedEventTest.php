<?php

/**
 * RSQueueBundle for Symfony2
 *
 * Marc Morera 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Event;

use Mmoreram\GearmanBundle\Event\GearmanClientCallbackCreatedEvent;

/**
 * Tests GearmanClientCallbackCreatedEventTest class
 */
class GearmanClientCallbackCreatedEventTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GearmanClientCallbackCreatedEvent
     *
     * Object to test
     */
    private $gearmanClientCallbackCreatedEvent;


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
        $this->gearmanClientCallbackCreatedEvent = new GearmanClientCallbackCreatedEvent($this->gearmanTask);
    }


    /**
     * Testing payload getter
     */
    public function testGetGearmanTask()
    {
        $this->assertEquals($this->gearmanClientCallbackCreatedEvent->getGearmanTask(), $this->gearmanTask);
    }


    /**
     * Tests if Event extends needed classes
    */
    public function testInstancesOf()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->gearmanClientCallbackCreatedEvent);
        $this->assertInstanceOf('Mmoreram\GearmanBundle\Event\Abstracts\AbstractGearmanClientTaskEvent', $this->gearmanClientCallbackCreatedEvent);
    }
}