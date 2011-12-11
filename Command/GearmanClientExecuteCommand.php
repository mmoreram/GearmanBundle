<?php

namespace Mmoreramerino\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Gearman Client Execute Command class
 *
 * @author Marc Morera <marc@ulabox.com>
 */
class GearmanClientExecuteCommand extends ContainerAwareCommand
{
    /**
     * Console Command configuration
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('gearman:client:execute')
             ->setDescription('Test')
             ->addArgument('job', InputArgument::REQUIRED, 'job to execute');
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
        $job = $input->getArgument('job');
        $this->getContainer()->get('gearman')->callJob($job);
    }
}