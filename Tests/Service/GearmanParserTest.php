<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Service;

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
    private $bundle;


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

        $this->assertEquals('My\File\Namespace\SingleCleanFile', $gearmanParser->getFileClassNamespace($mockNamespace));
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

        $this->assertEquals('My\File\Namespace\SingleCommentedFile', $gearmanParser->getFileClassNamespace($mockNamespace));
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
}