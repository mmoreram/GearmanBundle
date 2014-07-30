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

namespace Mmoreram\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Mmoreram\GearmanBundle\Command\Abstracts\AbstractGearmanCommand;
use Mmoreram\GearmanBundle\Service\GearmanCacheWrapper;

/**
 * Clears all cache data
 *
 * @since 2.3.1
 */
class GearmanCacheClearCommand extends AbstractGearmanCommand
{
    /**
     * @var GearmanCacheWrapper
     *
     * GearmanCacheWrapper
     */
    protected $gearmanCacheWrapper;

    /**
     * Set the GearmanCacheWrapper instance
     *
     * @param GearmanCacheWrapper $gearmanCacheWrapper GearmanCacheWrapper
     *
     * @return GearmanCacheWarmupCommand self Object
     */
    public function setGearmanCacheWrapper(GearmanCacheWrapper $gearmanCacheWrapper)
    {
        $this->gearmanCacheWrapper = $gearmanCacheWrapper;

        return $this;
    }

    /**
     * Console Command configuration
     */
    protected function configure()
    {
        $this
            ->setName('gearman:cache:clear')
            ->setAliases(array(
                'cache:gearman:clear'
            ))
            ->setDescription('Clears gearman cache data on current environment');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return integer 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract class is not implemented
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (
            !$input->getOption('quiet')
        ) {
            $kernelEnvironment = $this
                ->kernel
                ->getEnvironment();

            $output->writeln('Clearing the cache for the ' . $kernelEnvironment . ' environment');
        }
        $this
            ->gearmanCacheWrapper
            ->clear('');
    }
}
