<?php

/**
 * RSQueueBundle for Symfony2
 *
 * Marc Morera 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Event;

use Mmoreram\GearmanBundle\Event\GearmanClientCallbackWarningEvent;

/**
 * Tests GearmanClientCallbackWarningEventTest class
 */
class GearmanClientCallbackWarningEventTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GearmanClientCallbackWarningEvent
     *
     * Object to test
     */
    private $gearmanClientCallbackWarningEvent;


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
        $this->gearmanClientCallbackWarningEvent = new GearmanClientCallbackWarningEvent($this->gearmanTask);
    }


    /**
     * Testing payload getter
     */
    public function testGetGearmanTask()
    {
        $this->assertEquals($this->gearmanClientCallbackWarningEvent->getGearmanTask(), $this->gearmanTask);
    }
}