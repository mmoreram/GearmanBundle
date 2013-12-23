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
 * Tests GearmanExecute class
 */
class GearmanExecuteTest extends WebTestCase
{

    /**
     * Test service can be instanced through container
     */
    public function testGearmanExecuteLoadFromContainer()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->assertInstanceOf('\Mmoreram\GearmanBundle\Service\GearmanExecute', static::$kernel->getContainer()->get('gearman.execute'));
    }
}