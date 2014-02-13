<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Event;

use Mmoreram\GearmanBundle\Event\GearmanClientCallbackCompleteEvent;

/**
 * Tests GearmanClientCallbackCompleteEventTest class
 */
class GearmanClientCallbackCompleteEventTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GearmanClientCallbackCompleteEvent
     *
     * Object to test
     */
    private $gearmanClientCallbackCompleteEvent;

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
        $this->gearmanClientCallbackCompleteEvent = new GearmanClientCallbackCompleteEvent($this->gearmanTask);
    }

    /**
     * Testing payload getter
     */
    public function testGetGearmanTask()
    {
        $this->assertEquals($this->gearmanClientCallbackCompleteEvent->getGearmanTask(), $this->gearmanTask);
    }

    /**
     * Tests if Event extends needed classes
    */
    public function testInstancesOf()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->gearmanClientCallbackCompleteEvent);
        $this->assertInstanceOf('Mmoreram\GearmanBundle\Event\Abstracts\AbstractGearmanClientTaskEvent', $this->gearmanClientCallbackCompleteEvent);
    }
}
