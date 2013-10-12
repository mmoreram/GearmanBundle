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
 * Gearman Job Execute Command class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanJobExecuteCommand extends ContainerAwareCommand
{

    /**
     * Console Command configuration
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('gearman:job:execute')
             ->setDescription('Execute one single job')
             ->addArgument('job', InputArgument::REQUIRED, 'job to execute')
             ->addOption('no-description', null, InputOption::VALUE_NONE, 'Don\'t print job description');
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

        if (!$input->getOption('no-interaction') && !$dialog->askConfirmation($output, '<question>This will execute asked job?</question>', 'y')) {

            return;
        }

        $output->writeln(sprintf('<info>[%s] loading...</info>', date('Y-m-d H:i:s')));

        $job = $input->getArgument('job');
        $jobStruct = $this->getContainer()->get('gearman')->getJob($job);

        if (!$input->getOption('no-description')) {

            $this->getContainer()->get('gearman.describer')->describeJob($output, $jobStruct, true);
        }

        $output->writeln(sprintf('<info>[%s] loaded. Ctrl+C to break</info>', date('Y-m-d H:i:s')));
        $this->getContainer()->get('gearman.execute')->executeJob($job);
    }
}