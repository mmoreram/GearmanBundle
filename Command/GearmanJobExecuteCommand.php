<?php

namespace Ulabox\GearmanBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GearmanJobExecuteCommand extends ContainerAwareCommand
{
    /**
     * Console Command configuration
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('gearman:job:execute')
             ->setDescription('Execute one job of worker')
             ->addArgument('job', InputArgument::REQUIRED, 'Job to execute')
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
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$input->getOption('no-interaction') && !$dialog->askConfirmation($output, '<question>This will execute asked worker?</question>', 'y')) {
            return;
        }
        
        $job = $input->getArgument('job');
        $this->getContainer()->get('gearman.execute.job')->executeJob($job);
    }
}