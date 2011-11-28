<?php

namespace Mmoreramerino\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Gearman Job Describe Command class
 *
 * @author Marc Morera <marc@ulabox.com>
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
        $worker = $this->getContainer()->get('gearman')->getWorker($job);

        $output->writeln('');
        $output->writeln('<info>    @Worker\className : '.$worker['className'].'</info>');
        $output->writeln('<info>    @Worker\fileName : '.$worker['fileName'].'</info>');
        $output->writeln('<info>    @Worker\namespace : '.$worker['namespace'].'</info>');
        $output->writeln('<info>    @Worker-jobsnumber : '.count($worker['jobs']).'</info>');
        $output->writeln('<info>    @Worker\description :</info>');
        $output->writeln('');
        $output->writeln('<comment>        '.$worker['description'].'</comment>');
        $output->writeln('');
        $job = $worker['job'];
        $output->writeln('<info>    @job\methodName : '.$job['methodName'].'</info>');
        $output->writeln('<info>    @job\callableName : '.$job['realCallableName'].'</info>');
        $output->writeln('<info>    @job\iterations : '.$job['iterations'].'</info>');
        $output->writeln('<info>    @job\servers :</info>');
        $output->writeln('');
        foreach ($job['servers'] as $name => $server) {
            $output->writeln('<comment>        '.$name.' - '.$server.'</comment>');
        }
        $output->writeln('');
        $output->writeln('<info>    @job\description :</info>');
        $output->writeln('');
        $output->writeln('<comment>        '.$job['description'].'</comment>');
        $output->writeln('');
    }
}