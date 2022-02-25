<?php

namespace Mmoreram\GearmanBundle\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Mmoreram\GearmanBundle\Command\Abstracts\AbstractGearmanCommand;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use Mmoreram\GearmanBundle\Service\GearmanDescriber;
use Mmoreram\GearmanBundle\Service\GearmanExecute;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class GearmanJobExecuteCommand extends AbstractGearmanCommand
{
    protected GearmanClient $gearmanClient;
    protected GearmanDescriber $gearmanDescriber;
    protected GearmanExecute $gearmanExecute;

    public function setGearmanClient(GearmanClient $gearmanClient): self
    {
        $this->gearmanClient = $gearmanClient;

        return $this;
    }

    public function setGearmanDescriber(GearmanDescriber $gearmanDescriber): self
    {
        $this->gearmanDescriber = $gearmanDescriber;

        return $this;
    }

    public function setGearmanExecute(GearmanExecute $gearmanExecute): self
    {
        $this->gearmanExecute = $gearmanExecute;

        return $this;
    }

    protected function configure()
    {
        $this
            ->setName('gearman:job:execute')
            ->setDescription('Execute one single job')
            ->addArgument(
                'job',
                InputArgument::REQUIRED,
                'job to execute'
            )
            ->addOption(
                'no-description',
                null,
                InputOption::VALUE_NONE,
                'Don\'t print job description'
            )
            ->addOption(
                'iterations',
                null,
                InputOption::VALUE_OPTIONAL,
                'Override configured iterations'
            )
            ->addOption(
                'minimum-execution-time',
                null,
                InputOption::VALUE_OPTIONAL,
                'Override configured minimum execution time'
            )
            ->addOption(
                'timeout',
                null,
                InputOption::VALUE_OPTIONAL,
                'Override configured timeout'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**@var QuestionHelper $question*/
        $question =  $this->getHelper('question');

        if (
            !$input->getOption('no-interaction') &&
            !$question->ask(
                $input,
                $output,
                new ConfirmationQuestion('This will execute asked job?')
            )
        ) {
            return 0;
        }

        if (!$input->getOption('quiet')) {
            $output->writeln(
                sprintf(
                    '<info>[%s] loading...</info>',
                    date('Y-m-d H:i:s')
                )
            );
        }

        $job = $input->getArgument('job');
        $jobStructure = $this
            ->gearmanClient
            ->getJob($job);

        if (
            !$input->getOption('no-description') &&
            !$input->getOption('quiet')
        ) {
            $this
                ->gearmanDescriber
                ->describeJob(
                    $output,
                    $jobStructure,
                );
        }

        if (!$input->getOption('quiet')) {
            $output->writeln(
                sprintf(
                    '<info>[%s] loaded. Ctrl+C to break</info>',
                    date('Y-m-d H:i:s')
                )
            );
        }

        $this
            ->gearmanExecute
            ->setOutput($output)
            ->executeJob($job, [
                'iterations' => $input->getOption('iterations'),
                'minimum_execution_time' => $input->getOption('minimum-execution-time'),
                'timeout' => $input->getOption('timeout'),
            ]);

        return 0;
    }
}