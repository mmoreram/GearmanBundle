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
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

use Mmoreram\GearmanBundle\Command\GearmanWorkerExecuteCommand;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use Mmoreram\GearmanBundle\Service\GearmanDescriber;
use Mmoreram\GearmanBundle\Service\GearmanExecute;

/**
 * Class GearmanWorkerExecuteCommandTest
 */
class GearmanWorkerExecuteCommandTest extends TestCase
{
    /**
     * @var GearmanWorkerExecuteCommand
     *
     * Command
     */
    protected $command;

    /**
     * @var InputInterface
     *
     * Input
     */
    protected $input;

    /**
     * @var OutputInterface
     *
     * Output
     */
    protected $output;

    /**
     * @var QuestionHelper
     *
     * Question helper
     */
    protected $questionHelper;

    /**
     * @var GearmanClient
     *
     * Gearman client
     */
    protected $gearmanClient;

    /**
     * @var GearmanDescriber
     *
     * Gearman describer
     */
    protected $gearmanDescriber;

    /**
     * @var GearmanExecute
     *
     * Gearman execute
     */
    protected $gearmanExecute;

    /**
     * @var KernelInterface
     *
     * Kernel
     */
    protected $kernel;

    /**
     * setup
     */
    public function setUp(): void
    {
        $this->command = $this
            ->getMockBuilder('Mmoreram\GearmanBundle\Command\GearmanWorkerExecuteCommand')
            ->setMethods([
                'getHelperSet',
            ])
            ->getMock();

        $this->questionHelper = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->setMethods(['ask'])
            ->getMock();

        $helperSet = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\HelperSet')
            ->setMethods(['get'])
            ->getMock();

        $helperSet
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->questionHelper));

        $this
            ->command
            ->expects($this->any())
            ->method('getHelperSet')
            ->will($this->returnValue($helperSet));

        $this->input = $this
            ->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->output = $this
            ->getMockBuilder('Symfony\Component\Console\Output\OutputInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->kernel = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->gearmanClient = $this
            ->getMockBuilder('Mmoreram\GearmanBundle\Service\GearmanClient')
            ->disableOriginalConstructor()
            ->setMethods([
                'getWorker',
            ])
            ->getMock();

        $this->gearmanDescriber = $this
            ->getMockBuilder('Mmoreram\GearmanBundle\Service\GearmanDescriber')
            ->disableOriginalConstructor()
            ->setMethods([
                'describeWorker',
            ])
            ->getMock();

        $this->gearmanExecute = $this
            ->getMockBuilder('Mmoreram\GearmanBundle\Service\GearmanExecute')
            ->disableOriginalConstructor()
            ->setMethods([
                'executeWorker',
            ])
            ->getMock();

        $this->kernel
            ->expects($this->any())
            ->method('getEnvironment')
            ->will($this->returnValue('dev'));
    }

    /**
     * Test quietness
     *
     * @dataProvider dataQuietness
     */
    public function testQuietness(
        $quiet,
        $noInteraction,
        $confirmation,
        $countWriteln,
        $countDescriber,
        $countClient,
        $countExecute
    ) {
        $this
            ->input
            ->expects($this->any())
            ->method('getOption')
            ->will($this->returnValueMap([
                ['quiet', $quiet],
                ['no-interaction', $noInteraction],
            ]));

        $this
            ->questionHelper
            ->expects($this->any())
            ->method('ask')
            ->will($this->returnValue($confirmation));

        $this
            ->output
            ->expects($countWriteln)
            ->method('writeln');

        $this
            ->gearmanDescriber
            ->expects($countDescriber)
            ->method('describeWorker');

        $this
            ->gearmanClient
            ->expects($countClient)
            ->method('getWorker')
            ->will($this->returnValue([]));

        $this
            ->gearmanExecute
            ->expects($countExecute)
            ->method('executeWorker');

        $this->command
            ->setGearmanClient($this->gearmanClient)
            ->setGearmanDescriber($this->gearmanDescriber)
            ->setGearmanExecute($this->gearmanExecute)
            ->setKernel($this->kernel)
            ->run($this->input, $this->output);
    }

    /**
     * Data provider for testQuietness
     */
    public function dataQuietness()
    {
        return [
            [
                true,
                true,
                true,
                $this->never(),
                $this->never(),
                $this->atLeastOnce(),
                $this->atLeastOnce(),
            ],
            [
                true,
                true,
                false,
                $this->never(),
                $this->never(),
                $this->atLeastOnce(),
                $this->atLeastOnce(),
            ],
            [
                true,
                false,
                true,
                $this->never(),
                $this->never(),
                $this->atLeastOnce(),
                $this->atLeastOnce(),
            ],
            [
                true,
                false,
                false,
                $this->never(),
                $this->never(),
                $this->never(),
                $this->never(),
            ],
            [
                false,
                true,
                true,
                $this->atLeastOnce(),
                $this->atLeastOnce(),
                $this->atLeastOnce(),
                $this->atLeastOnce(),
            ],
            [
                false,
                true,
                false,
                $this->atLeastOnce(),
                $this->atLeastOnce(),
                $this->atLeastOnce(),
                $this->atLeastOnce(),
            ],
            [
                false,
                false,
                true,
                $this->atLeastOnce(),
                $this->atLeastOnce(),
                $this->atLeastOnce(),
                $this->atLeastOnce(),
            ],
            [
                false,
                false,
                false,
                $this->any(),
                $this->any(),
                $this->never(),
                $this->never(),
            ],
        ];
    }
}
