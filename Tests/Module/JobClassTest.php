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

namespace Mmoreram\GearmanBundle\Tests\Module;

use PHPUnit_Framework_TestCase;

use Mmoreram\GearmanBundle\Driver\Gearman\Job as JobAnnotation;
use Mmoreram\GearmanBundle\Module\JobClass;

/**
 * Tests JobClassTest class
 */
class JobClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var JobAnnotation
     *
     * Job annotation driver
     */
    private $jobAnnotation;

    /**
     * @var \ReflectionMethod
     *
     * Reflection Method
     */
    private $reflectionMethod;

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
    private $methodName = 'myMethod';

    /**
     * @var array
     *
     * Servers list
     */
    private $servers = array(
        array(
            'host' => '192.168.1.1',
            'port' => '8080',
        ),
    );

    /**
     * @var array
     *
     * Default settings
     */
    private $defaultSettings = array(
        'method'                         => 'doHigh',
        'iterations'                     => 100,
        'minimumExecutionTime'           => null,
        'timeout'                        => null,
        'callbacks'                      => true,
        'jobPrefix'                      => null,
        'generate_unique_key'            => true,
        'workers_name_prepend_namespace' => true,
    );

    /**
     * Setup
     */
    public function setUp()
    {
        $this->reflectionMethod = $this
            ->getMockBuilder('\ReflectionMethod')
            ->setConstructorArgs(array('\Mmoreram\GearmanBundle\Tests\Service\Mocks\SingleCleanFile', 'myMethod'))
            ->setMethods(array(
                'getName',
            ))
            ->getMock();

        $this->jobAnnotation = $this
            ->getMockBuilder('\Mmoreram\GearmanBundle\Driver\Gearman\Job')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Testing scenario with all Job annotations filled
     *
     * All settings given in annotations should be considered to configure Job
     *
     * Also testing server definition in JobAnnotation as an array of arrays ( multi server )
     */
    public function testJobAnnotationsDefined()
    {
        $this
            ->reflectionMethod
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($this->methodName));

        $this->jobAnnotation->name = 'myOtherMethodName';
        $this->jobAnnotation->description = 'This is my own description';
        $this->jobAnnotation->iterations = 200;
        $this->jobAnnotation->defaultMethod = 'doHighBackground';
        $this->jobAnnotation->servers = array(
            array(
                'host' => '10.0.0.2',
                'port' => '80',
            ),
        );

        $jobClass = new JobClass(
            $this->jobAnnotation,
            $this->reflectionMethod,
            $this->callableNameClass,
            $this->servers,
            $this->defaultSettings
        );

        $this->assertEquals($jobClass->toArray(), array(

            'callableName'             => $this->jobAnnotation->name,
            'methodName'               => $this->methodName,
            'realCallableName'         => str_replace('\\', '', $this->callableNameClass . '~' . $this->jobAnnotation->name),
            'jobPrefix'                => null,
            'realCallableNameNoPrefix' => str_replace('\\', '', $this->callableNameClass . '~' . $this->jobAnnotation->name),
            'description'              => $this->jobAnnotation->description,
            'iterations'               => $this->jobAnnotation->iterations,
            'minimumExecutionTime'     => $this->jobAnnotation->minimumExecutionTime,
            'timeout'                  => $this->jobAnnotation->timeout,
            'servers'                  => $this->jobAnnotation->servers,
            'defaultMethod'            => $this->jobAnnotation->defaultMethod,
        ));
    }

    /**
     * Testing scenario with any Job annotation filled
     *
     * All settings set as default should be considered to configure Job
     *
     * Also testing empty server definition in JobAnnotation
     */
    public function testJobAnnotationsEmpty()
    {
        $this
            ->reflectionMethod
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($this->methodName));

        $jobClass = new JobClass(
            $this->jobAnnotation,
            $this->reflectionMethod,
            $this->callableNameClass,
            $this->servers,
            $this->defaultSettings
        );

        $this->assertEquals($jobClass->toArray(), array(

            'callableName'             => $this->methodName,
            'methodName'               => $this->methodName,
            'realCallableName'         => str_replace('\\', '', $this->callableNameClass . '~' . $this->methodName),
            'jobPrefix'                => null,
            'realCallableNameNoPrefix' => str_replace('\\', '', $this->callableNameClass . '~' . $this->methodName),
            'description'              => $jobClass::DEFAULT_DESCRIPTION,
            'iterations'               => $this->defaultSettings['iterations'],
            'minimumExecutionTime'     => $this->defaultSettings['minimumExecutionTime'],
            'timeout'                  => $this->defaultSettings['timeout'],
            'servers'                  => $this->servers,
            'defaultMethod'            => $this->defaultSettings['method'],
        ));
    }

    /**
     * Testing specific server scenario configured in Job annotations as a simple server
     */
    public function testCombinationServers()
    {
        $this
            ->reflectionMethod
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($this->methodName));

        $this->jobAnnotation->servers = array(
            'host' => '10.0.0.2',
            'port' => '80',
        );

        $jobClass = new JobClass(
            $this->jobAnnotation,
            $this->reflectionMethod,
            $this->callableNameClass,
            $this->servers,
            $this->defaultSettings
        );

        $this->assertEquals($jobClass->toArray(), array(

            'callableName'             => $this->methodName,
            'methodName'               => $this->methodName,
            'realCallableName'         => str_replace('\\', '', $this->callableNameClass . '~' . $this->methodName),
            'jobPrefix'                => null,
            'realCallableNameNoPrefix' => str_replace('\\', '', $this->callableNameClass . '~' . $this->methodName),
            'description'              => $jobClass::DEFAULT_DESCRIPTION,
            'iterations'               => $this->defaultSettings['iterations'],
            'minimumExecutionTime'     => $this->defaultSettings['minimumExecutionTime'],
            'timeout'                  => $this->defaultSettings['timeout'],
            'servers'                  => array($this->jobAnnotation->servers),
            'defaultMethod'            => $this->defaultSettings['method'],
        ));
    }

    /**
     * Testing if giving a job a prefix that it uses it.
     */
    public function testJobPrefix()
    {
        $this
            ->reflectionMethod
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($this->methodName));

        $this->jobAnnotation->servers = array(
            'host' => '10.0.0.2',
            'port' => '80',
        );

        $settings = $this->defaultSettings;
        $settings['jobPrefix'] = 'test';

        $jobClass = new JobClass(
            $this->jobAnnotation,
            $this->reflectionMethod,
            $this->callableNameClass,
            $this->servers,
            $settings
        );

        $this->assertEquals($jobClass->toArray(), array(

            'callableName'             => $this->methodName,
            'methodName'               => $this->methodName,
            'realCallableName'         => 'test' . str_replace('\\', '', $this->callableNameClass . '~' . $this->methodName),
            'jobPrefix'                => 'test',
            'realCallableNameNoPrefix' => str_replace('\\', '', $this->callableNameClass . '~' . $this->methodName),
            'description'              => $jobClass::DEFAULT_DESCRIPTION,
            'iterations'               => $this->defaultSettings['iterations'],
            'minimumExecutionTime'     => $this->defaultSettings['minimumExecutionTime'],
            'timeout'                  => $this->defaultSettings['timeout'],
            'servers'                  => array($this->jobAnnotation->servers),
            'defaultMethod'            => $this->defaultSettings['method'],
        ));
    }
}
