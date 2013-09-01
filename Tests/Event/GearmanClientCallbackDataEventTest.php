<?php

/**
 * RSQueueBundle for Symfony2
 *
 * Marc Morera 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Event;

use Mmoreram\GearmanBundle\Event\GearmanClientCallbackDataEvent;

/**
 * Tests GearmanClientCallbackDataEventTest class
 */
class GearmanClientCallbackDataEventTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GearmanClientCallbackDataEvent
     *
     * Object to test
     */
    private $gearmanClientCallbackDataEvent;


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
        $this->gearmanClientCallbackDataEvent = new GearmanClientCallbackDataEvent($this->gearmanTask);
    }


    /**
     * Testing payload getter
     */
    public function testGetGearmanTask()
    {
        $this->assertEquals($this->gearmanClientCallbackDataEvent->getGearmanTask(), $this->gearmanTask);
    }
}