<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Gearman Job Describe Command class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanWorkerDescribeCommand extends ContainerAwareCommand
{

    /**
     * Console Command configuration
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('gearman:worker:describe')
             ->setDescription('Describe given worker')
             ->addArgument('worker', InputArgument::REQUIRED, 'worker to describe');
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
        $worker = $input->getArgument('worker');
        $worker = $this->getContainer()->get('gearman')->getWorker($worker);
        $this->getContainer()->get('gearman.describer')->describeWorker($output, $worker);
    }
}
