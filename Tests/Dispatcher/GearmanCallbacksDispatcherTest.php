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

namespace Mmoreram\GearmanBundle\Tests\Dispatcher;

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

        $this->assertInstanceOf(
            '\Mmoreram\GearmanBundle\Dispatcher\GearmanCallbacksDispatcher',
            static::$kernel
                ->getContainer()
                ->get('gearman.dispatcher.callbacks')
        );
    }
}
