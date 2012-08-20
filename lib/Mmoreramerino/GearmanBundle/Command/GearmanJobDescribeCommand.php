<?php

namespace Mmoreramerino\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Mmoreramerino\GearmanBundle\Module\JobClass;

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
        /** @var JobClass $job  */
        $job = $this->getContainer()->get('gearman')->getJob($job);
        $this->getContainer()->get('gearman.describer')->describeJob($output, $job);
    }
}