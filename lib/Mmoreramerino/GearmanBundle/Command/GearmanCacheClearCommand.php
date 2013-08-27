<?php

namespace Mmoreramerino\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Mmoreramerino\GearmanBundle\MmoreramerinoGearmanBundle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\Common\Cache\Cache;


/**
 * Warm ups all cache data
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanCacheClearCommand extends ContainerAwareCommand
{
    /**
     * Console Command configuration
     */
    protected function configure()
    {
        parent::configure();
        $this   ->setName('gearman:cache:clear')
                ->setAliases(array('cache:gearman:clear'))
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
        $output->writeln('Clearing the cache for the ' . $this->getContainer()->get('kernel')->getEnvironment() . ' environment');
        /** @var Cache $gearmanCache  */
        $gearmanCache = $this->getContainer()->get(MmoreramerinoGearmanBundle::CACHE_SERVICE);
        $gearmanCache->delete(MmoreramerinoGearmanBundle::CACHE_ID);
    }
}