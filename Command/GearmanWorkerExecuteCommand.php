<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Gearman Worker Execute Command class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanWorkerExecuteCommand extends ContainerAwareCommand
{

    /**
     * Console Command configuration
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('gearman:worker:execute')
             ->setDescription('Execute one worker with all contained Jobs')
             ->addArgument('worker', InputArgument::REQUIRED, 'work to execute')
             ->addOption('no-description', null, InputOption::VALUE_NONE, 'Don\'t print worker description');
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

        if (!$input->getOption('no-interaction') && !$dialog->askConfirmation($output, '<question>This will execute asked worker with all its jobs?</question>', 'y')) {

            return;
        }

        $output->writeln(sprintf('<info>[%s] loading...</info>', date('Y-m-d H:i:s')));

        $worker = $input->getArgument('worker');
        $workerStruct = $this->getContainer()->get('gearman')->getWorker($worker);

        if (!$input->getOption('no-description')) {

            $this->getContainer()->get('gearman.describer')->describeWorker($output, $workerStruct, true);
        }

        $output->writeln(sprintf('<info>[%s] loaded. Ctrl+C to break</info>', date('Y-m-d H:i:s')));
        $this->getContainer()->get('gearman.execute')->executeWorker($worker);
    }
}