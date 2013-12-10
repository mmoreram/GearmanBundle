<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Generator;

use Mmoreram\GearmanBundle\Generator\UniqueJobIdentifierGenerator;

/**
 * Gearman execute methods. All Worker methods
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class UniqueJobIdentifierGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests all cases when GenerateUniqueKey is false
     */
    public function testGenerateUniqueKeyFalse()
    {

        $generator = new UniqueJobIdentifierGenerator(false);

        $this->assertEquals('uniqueValue', $generator->generateUniqueKey(
            'name', 'params', 'uniqueValue', 'method'
        ));
        $this->assertFalse($generator->generateUniqueKey(
            'name', 'params', false, 'method'
        ));
        $this->assertNull($generator->generateUniqueKey(
            'name', 'params', null, 'method'
        ));
        $this->assertEquals('', $generator->generateUniqueKey(
            'name', 'params', '', 'method'
        ));
    }


    /**
     * Tests all cases when GenerateUniqueKey is true
     */
    public function testGenerateUniqueKeyTrue()
    {

        $generator = new UniqueJobIdentifierGenerator(true);
        $unique = 'c1af4ce5c9773ce30d8cc6d1e0e7d699';

        $this->assertEquals('uniqueValue', $generator->generateUniqueKey(
            'name', 'params', 'uniqueValue', 'method'
        ));
        $this->assertEquals($unique, $generator->generateUniqueKey(
            'name', 'params', false, 'method'
        ));
        $this->assertEquals($unique, $generator->generateUniqueKey(
            'name', 'params', null, 'method'
        ));
        $this->assertEquals($unique, $generator->generateUniqueKey(
            'name', 'params', '', 'method'
        ));
    }
}