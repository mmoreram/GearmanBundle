<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


/**
 * Warm ups all cache data
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanCacheWarmupCommand extends ContainerAwareCommand
{

    /**
     * Console Command configuration
     */
    protected function configure()
    {
        parent::configure();

        $this   ->setName('gearman:cache:warmup')
                ->setAliases(array('cache:gearman:warmup'))
                ->setDescription('Warms up gearman cache data');
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
        $output->writeln('Warming up the cache for the ' . $this->getContainer()->get('kernel')->getEnvironment() . ' environment');

        $this
            ->getContainer()
            ->get('gearman.cache.wrapper')
            ->flush()
            ->load();
    }
}