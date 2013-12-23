<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests GearmanCallbacksDispatcher class
 */
class GearmanCallbacksDispatcherTest extends WebTestCase
{

    /**
     * Test service can be instanced through container
     */
    public function testGearmanCallbacksDispatcherLoadFromContainer()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->assertInstanceOf('\Mmoreram\GearmanBundle\Dispatcher\GearmanCallbacksDispatcher', static::$kernel->getContainer()->get('gearman.dispatcher.callbacks'));
    }
}