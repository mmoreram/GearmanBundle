<?php

namespace Mmoreramerino\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class GearmanJobListCommand extends ContainerAwareCommand
{
    /**
     * Console Command configuration
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('gearman:job:list')
             ->setDescription('List all Gearman Jobs')
                ;
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
        $workers = $this->getContainer()->get('gearman')->getWorkers();
        if (is_array($workers)) {
            foreach ($workers as $worker) {
                $output->writeln('<info>    @'.$worker['className'].'</info>');
                foreach($worker['jobs'] as $job) {
                    $output->writeln('<comment>      #'.$job['realCallableName'].'</comment>');
                }
            }
        }
    }
}