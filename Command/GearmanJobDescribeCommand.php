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
class GearmanJobDescribeCommand extends ContainerAwareCommand
{

    /**
     * Console Command configuration
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('gearman:job:describe')
             ->setDescription('Describe given job')
             ->addArgument('job', InputArgument::REQUIRED, 'job to describe');
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
        $job = $this->getContainer()->get('gearman')->getJob($job);

        $this
            ->getContainer()
            ->get('gearman.describer')
            ->describeJob($output, $job);
    }
}
