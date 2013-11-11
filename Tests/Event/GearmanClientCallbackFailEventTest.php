<?php

/**
 * RSQueueBundle for Symfony2
 *
 * Marc Morera 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Event;

use Mmoreram\GearmanBundle\Event\GearmanClientCallbackFailEvent;

/**
 * Tests GearmanClientCallbackFailEventTest class
 */
class GearmanClientCallbackFailEventTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GearmanClientCallbackFailEvent
     *
     * Object to test
     */
    private $gearmanClientCallbackFailEvent;


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
        $this->gearmanClientCallbackFailEvent = new GearmanClientCallbackFailEvent($this->gearmanTask);
    }


    /**
     * Testing payload getter
     */
    public function testGetGearmanTask()
    {
        $this->assertEquals($this->gearmanClientCallbackFailEvent->getGearmanTask(), $this->gearmanTask);
    }


    /**
     * Tests if Event extends needed classes
    */
    public function testInstancesOf()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->gearmanClientCallbackFailEvent);
        $this->assertInstanceOf('Mmoreram\GearmanBundle\Event\Abstracts\AbstractGearmanClientTaskEvent', $this->gearmanClientCallbackFailEvent);
    }
}