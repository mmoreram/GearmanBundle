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
 * Tests GearmanDescriber class
 */
class GearmanDescriberTest extends WebTestCase
{

    /**
     * Test service can be instanced through container
     */
    public function testGearmanDescriberLoadFromContainer()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->assertInstanceOf('\Mmoreram\GearmanBundle\Service\GearmanDescriber', static::$kernel->getContainer()->get('gearman.describer'));
    }
}
