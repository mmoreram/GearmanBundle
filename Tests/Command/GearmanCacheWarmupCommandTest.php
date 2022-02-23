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

use Mmoreram\GearmanBundle\Command\GearmanCacheClearCommand;
use Mmoreram\GearmanBundle\Service\GearmanCacheWrapper;

/**
 * Class GearmanCacheWarmupCommandTest
 */
class GearmanCacheWarmupCommandTest extends TestCase
{
    /**
     * @var GearmanCacheClearCommand
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
     * @var GearmanCacheWrapper
     *
     * Gearman Cache Wrapper
     */
    protected $gearmanCacheWrapper;

    /**
     * @var KernelInterface
     *
     * Kernel
     */
    protected $kernel;

    /**
     * Set up method
     */
    public function setUp(): void
    {
        $this->command = $this
            ->getMockBuilder('Mmoreram\GearmanBundle\Command\GearmanCacheWarmupCommand')
            ->setMethods(null)
            ->getMock();

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

        $this->gearmanCacheWrapper = $this
            ->getMockBuilder('Mmoreram\GearmanBundle\Service\GearmanCacheWrapper')
            ->disableOriginalConstructor()
            ->setMethods([
                'warmup',
            ])
            ->getMock();

        $this
            ->gearmanCacheWrapper
            ->expects($this->once())
            ->method('warmup');

        $this
            ->kernel
            ->expects($this->any())
            ->method('getEnvironment')
            ->will($this->returnValue('dev'));

        $this
            ->command
            ->setGearmanCacheWrapper($this->gearmanCacheWrapper)
            ->setKernel($this->kernel);
    }

    /**
     * Test run quietness
     */
    public function testRunQuiet()
    {
        $this
            ->input
            ->expects($this->any())
            ->method('getOption')
            ->will($this->returnValueMap([
                ['quiet', true],
            ]));

        $this
            ->output
            ->expects($this->never())
            ->method('writeln');

        $this
            ->command
            ->run(
                $this->input,
                $this->output
            );
    }

    /**
     * Test run without quietness
     */
    public function testRunNonQuiet()
    {
        $this
            ->input
            ->expects($this->any())
            ->method('getOption')
            ->will($this->returnValueMap([
                ['quiet', false],
            ]));

        $this
            ->output
            ->expects($this->any())
            ->method('writeln');

        $this
            ->command
            ->run(
                $this->input,
                $this->output
            );
    }
}
