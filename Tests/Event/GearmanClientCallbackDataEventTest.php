<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
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

    /**
     * Tests if Event extends needed classes
    */
    public function testInstancesOf()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->gearmanClientCallbackDataEvent);
        $this->assertInstanceOf('Mmoreram\GearmanBundle\Event\Abstracts\AbstractGearmanClientTaskEvent', $this->gearmanClientCallbackDataEvent);
    }
}
