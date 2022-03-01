<?php

namespace Mmoreram\GearmanBundle\Service;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class GearmanDescriber
{

    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

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
        /**
         * Commandline
         */
        $script = $this->getConsolePath() . 'gearman:job:execute';

        /**
         * A job descriptions contains its worker description
         */
        $this->describeWorker($output, $worker);

        $job = $worker['job'];
        $output->writeln('<info>@job\methodName : ' . $job['methodName'] . '</info>');
        $output->writeln('<info>@job\callableName : ' . $job['realCallableName'] . '</info>');

        if ($job['jobPrefix']) {
            $output->writeln('<info>@job\jobPrefix : ' . $job['jobPrefix'] . '</info>');
        }

        /**
         * Also a complete and clean execution path is given , for supervisord
         */
        $output->writeln('<info>@job\supervisord : </info><comment>/usr/bin/php ' . $script.' ' . $job['realCallableName'] . ' --no-interaction</comment>');
        $output->writeln('<info>@job\iterations : ' . $job['iterations'] . '</info>');
        $output->writeln('<info>@job\defaultMethod : ' . $job['defaultMethod'] . '</info>');

        /**
         * Printed every server is defined for current job
         */
        $output->writeln('');
        $output->writeln('<info>@job\servers :</info>');
        $output->writeln('');
        foreach ($job['servers'] as $name => $server) {
            $output->writeln('<comment>    ' . $name . ' - ' . $server['host'] . ':' . $server['port'] . '</comment>');
        }

        /**
         * Description
         */
        $output->writeln('');
        $output->writeln('<info>@job\description :</info>');
        $output->writeln('');
        $output->writeln('<comment>    #' . $job['description'] . '</comment>');
        $output->writeln('');
    }

    /**
     * Describe Worker.
     *
     * Given a output object and a Worker, dscribe it.
     *
     * @param OutputInterface $output             Output object
     * @param array           $worker             Worker array with Job to describe
     * @param Boolean         $tinyJobDescription If true also print job list
     */
    public function describeWorker(OutputInterface $output, array $worker, $tinyJobDescription = false)
    {
        /**
         * Commandline
         */
        $script = $this->getConsolePath() . ' gearman:worker:execute';

        $output->writeln('');
        $output->writeln('<info>@Worker\className : ' . $worker['className'] . '</info>');
        $output->writeln('<info>@Worker\fileName : ' . $worker['fileName'] . '</info>');
        $output->writeln('<info>@Worker\nameSpace : ' . $worker['namespace'] . '</info>');
        $output->writeln('<info>@Worker\callableName: ' . $worker['callableName'] . '</info>');

        /**
         * Also a complete and clean execution path is given , for supervisord
         */
        $output->writeln('<info>@Worker\supervisord : </info><comment>/usr/bin/php ' . $script.' ' . $worker['callableName'] . ' --no-interaction</comment>');

        /**
         * Service value is only explained if defined. Not mandatory
         */
        if (null !== $worker['service']) {
            $output->writeln('<info>@Worker\service : ' . $worker['service'] . '</info>');
        }

        $output->writeln('<info>@worker\iterations : ' . $worker['iterations'] . '</info>');
        $output->writeln('<info>@Worker\#jobs : ' . count($worker['jobs']) . '</info>');

        if ($tinyJobDescription) {
            $output->writeln('<info>@Worker\jobs</info>');
            $output->writeln('');
            foreach ($worker['jobs'] as $job) {
                if ($job['jobPrefix']) {
                    $output->writeln('<comment>    # ' . $job['realCallableNameNoPrefix'] . ' with jobPrefix: ' . $job['jobPrefix'] . '</comment>');
                } else {
                    $output->writeln('<comment>    # ' . $job['realCallableNameNoPrefix'] . ' </comment>');
                }
            }
        }

        /**
         * Printed every server is defined for current job
         */
        $output->writeln('');
        $output->writeln('<info>@worker\servers :</info>');
        $output->writeln('');
        foreach ($worker['servers'] as $name => $server) {
            $output->writeln('<comment>    #' . $name . ' - ' . $server['host'] . ':' . $server['port'] . '</comment>');
        }

        /**
         * Description
         */
        $output->writeln('');
        $output->writeln('<info>@Worker\description :</info>');
        $output->writeln('');
        $output->writeln('<comment>    ' . $worker['description'] . '</comment>');
        $output->writeln('');
    }

    private function getConsolePath()
    {
        $projectDir = $this->kernel->getProjectDir();

        if (true === file_exists($projectDir.'/bin/console')) {
            return realpath($projectDir. '/bin/console');
        } else {
            return realpath($projectDir. '/app/console');
        }
    }
}
