<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Service;

use Mmoreram\GearmanBundle\Module\WorkerCollection;

/**
 * Tests JobClassTest class
 */
class GearmanParserTest extends \PHPUnit_Framework_TestCase
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
     * testing getFileClassNamespace
     */
    public function testGetFileClassNamespaceSingle()
    {
        $mockNamespace = dirname(__FILE__) . '/Mocks/SingleCleanFile.php';

        $gearmanParser = $this
            ->getMockBuilder('\Mmoreram\GearmanBundle\Service\GearmanParser')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->assertEquals('Mmoreram\GearmanBundle\Tests\Service\Mocks\SingleCleanFile', $gearmanParser->getFileClassNamespace($mockNamespace));
    }


    /**
     * testing getFileClassNamespace
     */
    public function testGetFileClassNamespaceCommented()
    {
        $mockNamespace = dirname(__FILE__) . '/Mocks/SingleCommentedFile.php';

        $gearmanParser = $this
            ->getMockBuilder('\Mmoreram\GearmanBundle\Service\GearmanParser')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->assertEquals('Mmoreram\GearmanBundle\Tests\Service\Mocks\SingleCommentedFile', $gearmanParser->getFileClassNamespace($mockNamespace));
    }


    /**
     * Testing loadNamespaceMap without Include and Exclude values
     */
    public function testLoadNamespaceMapSimple()
    {

        $this->bundleMock
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($this->bundlePath));

        $this->kernelBundles = array(

            "FirstBundleName" => $this->bundleMock,
        );

        list($paths, $excludedPaths) = $this->gearmanParser->loadNamespaceMap($this->kernelBundles, array(
            "FirstBundle" => array(
                "name"      =>  "FirstBundleName",
                "active"    =>  true,
                "include"   =>  array(),
                "ignore"    =>  array(),
            ),
        ));

        $this->assertEquals($paths, array($this->bundlePath . '/'));
        $this->assertEquals($excludedPaths, array());
    }


    /**
     * Testing loadNamespaceMap with just Include values
     */
    public function testLoadNamespaceMapIncludes()
    {

        $this->bundleMock
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($this->bundlePath));

        $this->kernelBundles = array(

            "FirstBundleName" => $this->bundleMock,
        );

        list($paths, $excludedPaths) = $this->gearmanParser->loadNamespaceMap($this->kernelBundles, array(
            "FirstBundle" => array(
                "name"      =>  "FirstBundleName",
                "active"    =>  true,
                "include"   =>  array(
                    'Services',
                    'Workers',
                ),
                "ignore"    =>  array(),
            ),
        ));

        $this->assertEquals($paths, array(
            $this->bundlePath . '/Services/',
            $this->bundlePath . '/Workers/',
        ));
        $this->assertEquals($excludedPaths, array());
    }


    /**
     * Testing loadNamespaceMap with just exclude values
     */
    public function testLoadNamespaceMapExcludes()
    {

        $this->bundleMock
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($this->bundlePath));

        $this->kernelBundles = array(

            "FirstBundleName" => $this->bundleMock,
        );

        list($paths, $excludedPaths) = $this->gearmanParser->loadNamespaceMap($this->kernelBundles, array(
            "FirstBundle" => array(
                "name"      =>  "FirstBundleName",
                "active"    =>  true,
                "include"   =>  array(),
                "ignore"    =>  array(
                    'Services',
                    'Workers',
                ),
            ),
        ));

        $this->assertEquals($paths, array($this->bundlePath . '/'));
        $this->assertEquals($excludedPaths, array(
            'Services',
            'Workers',
        ));
    }


    /**
     * Testing loadNamespaceMap with includes and exclude values
     */
    public function testLoadNamespaceMapBoth()
    {

        $this
            ->bundleMock
            ->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue($this->bundlePath));

        $this->kernelBundles = array(

            "FirstBundleName" => $this->bundleMock,
        );

        list($paths, $excludedPaths) = $this->gearmanParser->loadNamespaceMap($this->kernelBundles, array(
            "FirstBundle" => array(
                "name"      =>  "FirstBundleName",
                "active"    =>  true,
                "include"   =>  array(
                    'Controllers',
                    'libs'
                ),
                "ignore"    =>  array(
                    'Services',
                    'Workers',
                    'libs',
                ),
            ),
        ));

        $this->assertEquals($paths, array(
            $this->bundlePath . '/Controllers/',
            $this->bundlePath . '/libs/',

        ));
        $this->assertEquals($excludedPaths, array(
            'Services',
            'Workers',
            'libs',
        ));
    }


    /**
     * Testing parseNamespaceMap with empty paths
     */
    public function testParseNamespaceMapEmptyPaths()
    {
        $paths = array();
        $excludedPaths = array();

        $reader = $this
            ->getMockBuilder('\Doctrine\Common\Annotations\Reader')
            ->disableOriginalConstructor()
            ->getMock();

        $finder = $this
            ->getMockBuilder('\Symfony\Component\Finder\Finder')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'files',
            ))
            ->getMock();

        $finder
            ->expects($this->any())
            ->method('getPath');

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












}