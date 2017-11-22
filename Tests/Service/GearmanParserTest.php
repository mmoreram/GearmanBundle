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

namespace Mmoreram\GearmanBundle\Tests\Service;

use Doctrine\Common\Annotations\Reader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Mmoreram\GearmanBundle\Module\WorkerCollection;
use Mmoreram\GearmanBundle\Service\GearmanParser;

/**
 * Tests GearmanParser class
 */
class GearmanParserTest extends WebTestCase
{
    /**
     * @var GearmanParser
     *
     * GearmanParser mock
     */
    private $gearmanParser;

    /**
     * @var Bundle
     *
     * Bundle mock
     */
    private $bundleMock;

    /**
     * @var array
     *
     * KernelBundles
     */
    private $kernelBundles;

    /**
     * @var string
     *
     * Bundle path
     */
    private $bundlePath = '/my/bundle/path';

    /**
     * Setup
     */
    public function setUp()
    {
        $this->gearmanParser = $this
            ->getMockBuilder('\Mmoreram\GearmanBundle\Service\GearmanParser')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->bundleMock = $this
            ->getMockBuilder('\Symfony\Component\HttpKernel\Bundle\Bundle')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'getPath'
            ))
            ->getMock();
    }

    /**
     * Test service can be instanced through container
     */
    public function testGearmanParserLoadFromContainer()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->assertInstanceOf(
            '\Mmoreram\GearmanBundle\Service\GearmanParser',
            static::$kernel
                ->getContainer()
                ->get('gearman.parser')
        );
    }

    /**
     * testing getFileClassNamespace
     */
    public function testGetFileClassNamespaceSingle()
    {
        $mockNamespace = dirname(__FILE__) . '/Mocks/SingleCleanFile.php';

        /**
         * @var GearmanParser $gearmanParser
         */
        $gearmanParser = $this
            ->getMockBuilder('\Mmoreram\GearmanBundle\Service\GearmanParser')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->assertEquals(
            'Mmoreram\GearmanBundle\Tests\Service\Mocks\SingleCleanFile',
            $gearmanParser->getFileClassNamespace($mockNamespace)
        );
    }

    /**
     * testing getFileClassNamespace
     */
    public function testGetFileClassNamespaceCommented()
    {
        $mockNamespace = dirname(__FILE__) . '/Mocks/SingleCommentedFile.php';

        /**
         * @var GearmanParser $gearmanParser
         */
        $gearmanParser = $this
            ->getMockBuilder('\Mmoreram\GearmanBundle\Service\GearmanParser')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->assertEquals(
            'Mmoreram\GearmanBundle\Tests\Service\Mocks\SingleCommentedFile',
            $gearmanParser->getFileClassNamespace($mockNamespace)
        );
    }

    /**
     * Testing parseNamespaceMap with empty paths
     */
    public function testParseNamespaceMapEmptyPaths()
    {
        $paths = array();
        $excludedPaths = array();

        /**
         * @var Reader $reader
         */
        $reader = $this
            ->getMockBuilder('\Doctrine\Common\Annotations\Reader')
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * @var Finder $finder
         */
        $finder = $this
            ->getMockBuilder('\Symfony\Component\Finder\Finder')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'files',
            ))
            ->getMock();

        $workerCollection = $this
            ->gearmanParser
            ->parseNamespaceMap($finder, $reader, $paths, $excludedPaths);

        $this->assertEquals($workerCollection, new workerCollection());
    }

    /**
     * Testing parseNamespaceMap with some paths
     */
    public function testParseNamespaceMapSomePaths()
    {
        $this->gearmanParser = $this
            ->getMockBuilder('\Mmoreram\GearmanBundle\Service\GearmanParser')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'parseFiles',
            ))
            ->getMock();

        $paths = array(
            dirname(__FILE__) . '/Mocks/',

        );
        $excludedPaths = array();

        $reader = $this
            ->getMockBuilder('\Doctrine\Common\Annotations\SimpleAnnotationReader')
            ->setMethods(null)
            ->getMock();

        $finder = $this
            ->getMockBuilder('\Symfony\Component\Finder\Finder')
            ->setMethods(null)
            ->getMock();

        $this
            ->gearmanParser
            ->expects($this->once())
            ->method('parseFiles')
            ->with(
                $this->equalTo($finder),
                $this->equalTo($reader),
                $this->equalTo(new WorkerCollection)
            );

        $workerCollection = $this
            ->gearmanParser
            ->parseNamespaceMap($finder, $reader, $paths, $excludedPaths);

        $this->assertEquals($workerCollection, new workerCollection());
    }

    /**
     * Testing parseNamespaceMap with some paths
     *
     * @dataProvider loadBundleNamespaceMapDataProvider
     */
    public function testLoadBundleNamespaceMap($active, $include, $ignore, $expectedPaths, $expectedExcludedPaths)
    {
        $this
            ->bundleMock
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($this->bundlePath));

        $this->kernelBundles = array(

            "FirstBundleName" => $this->bundleMock,
        );

        list($paths, $excludedPaths) = $this->gearmanParser->loadBundleNamespaceMap($this->kernelBundles, array(
            "FirstBundle" => array(
                "name"      =>  "FirstBundleName",
                "active"    =>  $active,
                "include"   =>  $include,
                "ignore"    =>  $ignore,
            ),
        ));

        $this->assertEquals($paths, $expectedPaths);
        $this->assertEquals($excludedPaths, $expectedExcludedPaths);
    }

    /**
     * Testing loadResourceNamespaceMap
     */
    public function testLoadResourceNamespaceMap()
    {
        $rootDir = '/app/kernel/root/directory';

        $data = array(
            '/Worker/' => $rootDir . '/Worker/',
            'Infrastructure/Gearman/Workers' => $rootDir . '/Infrastructure/Gearman/Workers/',
        );

        $this->gearmanParser = $this
            ->getMockBuilder('\Mmoreram\GearmanBundle\Service\GearmanParser')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $paths = $this->gearmanParser->loadResourceNamespaceMap($rootDir, array_keys($data));

        $this->assertEquals($paths, array_values($data));
    }

    /**
     * Load bundle namespace map Data Provider
     */
    public function loadBundleNamespaceMapDataProvider()
    {
        return array(

            // Testing loadNamespaceMap with includes and exclude values
            array(
                true,
                array(
                    'Controllers',
                    'libs'
                ),
                array(
                    'Services',
                    'Workers',
                    'libs',
                ),
                array(
                    $this->bundlePath . '/Controllers/',
                    $this->bundlePath . '/libs/',
                ),
                array(
                    'Services',
                    'Workers',
                    'libs',
                )
            ),

            // Testing loadNamespaceMap without Include and Exclude values
            array(
                true,
                array(),
                array(),
                array(
                    $this->bundlePath . '/',
                ),
                array(),
            ),

            // Testing loadNamespaceMap with just exclude values
            array(
                true,
                array(),
                array(
                    'Services',
                    'Workers',
                ),
                array(
                    $this->bundlePath . '/',
                ),
                array(
                    'Services',
                    'Workers',
                ),
            ),

            // Testing loadNamespaceMap with just Include values
            array(
                true,
                array(
                    'Services',
                    'Workers',
                ),
                array(),
                array(
                    $this->bundlePath . '/Services/',
                    $this->bundlePath . '/Workers/',
                ),
                array(),
            ),

            // Testing loadNamespaceMap with invalid bundle
            array(
                false,
                array(
                    'Services',
                    'Workers',
                ),
                array(
                    'Ignore',
                    'Tests',
                ),
                array(),
                array(),
            )
        );
    }

}
