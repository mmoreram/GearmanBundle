<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Event;

use Mmoreram\GearmanBundle\Event\GearmanClientCallbackExceptionEvent;

/**
 * Tests GearmanClientCallbackExceptionEventTest class
 */
class GearmanClientCallbackExceptionEventTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GearmanClientCallbackExceptionEvent
     *
     * Object to test
     */
    private $gearmanClientCallbackExceptionEvent;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->gearmanClientCallbackExceptionEvent = new GearmanClientCallbackExceptionEvent($this->gearmanTask);
    }

    /**
     * Tests if Event extends needed classes
    */
    public function testInstancesOf()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->gearmanClientCallbackExceptionEvent);
    }
}
