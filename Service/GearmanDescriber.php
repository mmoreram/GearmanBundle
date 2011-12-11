<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Implementation of GearmanDescriber
 *
 * @author Marc Morera <marc@ulabox.com>
 */
class GearmanDescriber extends ContainerAware
{

    /**
     * Describe Job.
     *
     * Given a output object and a Job, dscribe it.
     *
     * @param OutputInterface $output Output object
     * @param array           $worker Worker array with Job to describe
     */
    public function describeJob(OutputInterface $output, array $worker)
    {
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
        $output->writeln('<info>    @job\defaultMethod : '.$job['defaultMethod'].'</info>');
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