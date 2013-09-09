<?php

/**
 * RSQueueBundle for Symfony2
 *
 * Marc Morera 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Module;

use Mmoreram\GearmanBundle\Module\JobClass;
use Mmoreram\GearmanBundle\Module\WorkerClass;
use Mmoreram\GearmanBundle\Driver\Gearman\Work as WorkAnnotation;
use Mmoreram\GearmanBundle\Driver\Gearman\Job as JonAnnotation;

/**
 * Tests JobClassTest class
 */
class WorkerClassTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var WorkAnnotation
     * 
     * Worker annotation driver
     */
    private $workerAnnotation;


    /**
     * @var \ReflectionClass
     * 
     * Reflection Class
     */
    private $reflectionClass;


    /**
     * @var Reader
     * 
     * Reader
     */
    private $reader;


    /**
     * @var string
     * 
     * Class namespace
     */
    private $classNamespace = 'MyClassNamespace';


    /**
     * @var string
     * 
     * Callable name
     */
    private $callableNameClass = 'MyClassCallablaName';


    /**
     * @var string
     * 
     * Class name
     */
    private $className = 'myClass';


    /**
     * @var string
     * 
     * Filename
     */
    private $fileName = 'myClass.php';


    /**
     * @var array
     * 
     * Servers list
     */
    private $servers = array(
        array(
            'host'  =>  '192.168.1.1',
            'port'  =>  '8080',
        ),
    );


    /**
     * @var array
     * 
     * Default settings
     */
    private $defaultSettings = array(
        'method'        =>  'doHigh',
        'iterations'    =>  100,
    );


    /**
     * Setup
     */
    public function setUp()
    {

        $this->reflectionClass = $this  ->getMockBuilder('\ReflectionClass')
                                        ->disableOriginalConstructor()
                                        ->setMethods(array(
                                            'getName',
                                            'getNamespaceName',
                                            'getFileName',
                                            'getMethods',
                                        ))
                                        ->getMock();

        $this->workAnnotation = $this   ->getMockBuilder('\Mmoreram\GearmanBundle\Driver\Gearman\Work')
                                        ->disableOriginalConstructor()
                                        ->getMock();

        $this->reader = $this   ->getMockBuilder('Doctrine\Common\Annotations\SimpleAnnotationReader')
                                ->disableOriginalConstructor()
                                ->setMethods(array(
                                    'getMethodAnnotations'
                                ))
                                ->getMock();
    }


    /**
     * Testing scenario with all Job annotations filled
     * 
     * All settings given in annotations should be considered to configure Job
     * 
     * Also testing server definition in JobAnnotation as an array of arrays ( multi server )
     */
    public function testWorkerAnnotationsDefined()
    {

        $this
            ->reflectionClass
            ->expects($this->once())
            ->method('getNamespaceName')
            ->will($this->returnValue($this->classNamespace));

        $this
            ->reflectionClass
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($this->className));

        $this
            ->reflectionClass
            ->expects($this->once())
            ->method('getFileName')
            ->will($this->returnValue($this->fileName));

        $this
            ->reflectionClass
            ->expects($this->once())
            ->method('getMethods')
            ->will($this->returnValue(array()));

        $this
            ->reader
            ->expects($this->any())
            ->method('getMethodAnnotations');

        $this->workAnnotation->name = 'myOtherWorkerName';
        $this->workAnnotation->description = 'This is my own description';
        $this->workAnnotation->iterations = 200;
        $this->workAnnotation->defaultMethod = 'doHighBackground';
        $this->workAnnotation->service = 'my.service';
        $this->workAnnotation->servers = array(
            array(
                'host'  =>  '10.0.0.2',
                'port'  =>  '80',
            ),
        );

        $workerClass = new WorkerClass($this->workAnnotation, $this->reflectionClass, $this->reader, $this->servers, $this->defaultSettings);
        $this->assertEquals($workerClass->toArray(), array(

            'namespace'             =>  $this->classNamespace,
            'className'             =>  $this->className,
            'fileName'              =>  $this->fileName,
            'callableName'          =>  $this->classNamespace . $this->workAnnotation->name,
            'description'           =>  $this->workAnnotation->description,
            'service'               =>  $this->workAnnotation->service,
            'servers'               =>  $this->workAnnotation->servers,
            'iterations'            =>  $this->workAnnotation->iterations,
            'jobs'                  =>  array(),
        ));
    }


    /**
     * Testing scenario with any Job annotation filled
     * 
     * All settings set as default should be considered to configure Job
     * 
     * Also testing empty server definition in JobAnnotation
     */
    public function testWorkerAnnotationsEmpty()
    {

        $this
            ->reflectionClass
            ->expects($this->once())
            ->method('getNamespaceName')
            ->will($this->returnValue($this->classNamespace));

        $this
            ->reflectionClass
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($this->className));

        $this
            ->reflectionClass
            ->expects($this->once())
            ->method('getFileName')
            ->will($this->returnValue($this->fileName));

        $this
            ->reflectionClass
            ->expects($this->once())
            ->method('getMethods')
            ->will($this->returnValue(array()));

        $this
            ->reader
            ->expects($this->any())
            ->method('getMethodAnnotations');

        $workerClass = new WorkerClass($this->workAnnotation, $this->reflectionClass, $this->reader, $this->servers, $this->defaultSettings);
        $this->assertEquals($workerClass->toArray(), array(

            'namespace'             =>  $this->classNamespace,
            'className'             =>  $this->className,
            'fileName'              =>  $this->fileName,
            'callableName'          =>  $this->className,
            'description'           =>  $workerClass::DEFAULT_DESCRIPTION,
            'service'               =>  null,
            'servers'               =>  $this->servers,
            'iterations'            =>  $this->defaultSettings['iterations'],
            'jobs'                  =>  array(),
        ));
    }


    /**
     * Testing specific server scenario configured in Job annotations as a simple server
     */
    public function testCombinationServers()
    {

        $this
            ->reflectionClass
            ->expects($this->once())
            ->method('getNamespaceName')
            ->will($this->returnValue($this->classNamespace));

        $this
            ->reflectionClass
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($this->className));

        $this
            ->reflectionClass
            ->expects($this->once())
            ->method('getFileName')
            ->will($this->returnValue($this->fileName));

        $this
            ->reflectionClass
            ->expects($this->once())
            ->method('getMethods')
            ->will($this->returnValue(array()));

        $this
            ->reader
            ->expects($this->any())
            ->method('getMethodAnnotations');

        $this->workAnnotation->servers = array(
            'host'  =>  '10.0.0.2',
            'port'  =>  '80',
        );

        $workerClass = new WorkerClass($this->workAnnotation, $this->reflectionClass, $this->reader, $this->servers, $this->defaultSettings);
        $this->assertEquals($workerClass->toArray(), array(

            'namespace'             =>  $this->classNamespace,
            'className'             =>  $this->className,
            'fileName'              =>  $this->fileName,
            'callableName'          =>  $this->className,
            'description'           =>  $workerClass::DEFAULT_DESCRIPTION,
            'service'               =>  null,
            'servers'               =>  array($this->workAnnotation->servers),
            'iterations'            =>  $this->defaultSettings['iterations'],
            'jobs'                  =>  array(),
        ));
    }
}