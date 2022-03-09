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

namespace Mmoreram\GearmanBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\GearmanBundle\Command\GearmanJobDescribeCommand;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use Mmoreram\GearmanBundle\Service\GearmanDescriber;

/**
 * Class GearmanWorkerDescribeCommandTest
 */
class GearmanWorkerDescribeCommandTest extends TestCase
{
    /**
     * test run
     */
    public function testRun()
    {
        $worker = ['xxx'];

        /**
         * @var GearmanJobDescribeCommand $command
         * @var InputInterface $input
         * @var OutputInterface $output
         * @var KernelInterface $kernel
         * @var GearmanClient $gearmanClient
         * @var GearmanDescriber $gearmanDescriber
         */
        $command = $this
            ->getMockBuilder('Mmoreram\GearmanBundle\Command\GearmanWorkerDescribeCommand')
            ->setMethods(null)
            ->getMock();

        $input = $this
            ->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $output = $this
            ->getMockBuilder('Symfony\Component\Console\Output\OutputInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $kernel = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $gearmanClient = $this
            ->getMockBuilder('Mmoreram\GearmanBundle\Service\GearmanClient')
            ->disableOriginalConstructor()
            ->setMethods([
                'getWorker',
            ])
            ->getMock();

        $gearmanClient
            ->expects($this->once())
            ->method('getWorker')
            ->will($this->returnValue($worker));

        $gearmanDescriber = $this
            ->getMockBuilder('Mmoreram\GearmanBundle\Service\GearmanDescriber')
            ->disableOriginalConstructor()
            ->setMethods([
                'describeWorker',
            ])
            ->getMock();

        $gearmanDescriber
            ->expects($this->once())
            ->method('describeWorker')
            ->with($this->equalTo($output), $this->equalTo($worker));

        $kernel
            ->expects($this->any())
            ->method('getEnvironment')
            ->will($this->returnValue('dev'));

        $command
            ->setGearmanClient($gearmanClient)
            ->setGearmanDescriber($gearmanDescriber)
            ->setKernel($kernel)
            ->run($input, $output);
    }
}
