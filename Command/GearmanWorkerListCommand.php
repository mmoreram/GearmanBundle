<?php

namespace Mmoreram\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Mmoreram\GearmanBundle\Command\Abstracts\AbstractGearmanCommand;
use Mmoreram\GearmanBundle\Service\GearmanClient;

class GearmanWorkerListCommand extends AbstractGearmanCommand
{
    protected GearmanClient $gearmanClient;

    public function setGearmanClient(GearmanClient $gearmanClient): self
    {
        $this->gearmanClient = $gearmanClient;

        return $this;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('gearman:worker:list')
            ->setDescription('List all Gearman Workers and their Jobs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('quiet')) {
            return 0;
        }

        $workers = $this->gearmanClient->getWorkers();

        if (is_array($workers)) {
            $it = 1;

            foreach ($workers as $worker) {
                $output->writeln('<comment>@Worker:  </comment><info>' . $worker['className'] . '</info>');
                $output->writeln('<comment>callablename:  </comment><info>' . $worker['callableName'] . '</info>');
                $output->writeln('<comment>Jobs:</comment>');
                foreach ($worker['jobs'] as $job) {
                    $output->writeln('<comment>  - #' . $it++ . '</comment>');
                    $output->writeln('<comment>      name: ' . $job['methodName'] . '</comment>');
                    $output->writeln(
                        '<comment>      callablename:</comment><info> ' . $job['realCallableNameNoPrefix'] . '</info>'
                    );

                    if (false === is_null($job['jobPrefix'])) {
                        $output->writeln('<comment>      jobPrefix:</comment><info> ' . $job['jobPrefix'] . '</info>');
                    }
                }
            }
        }

        return 0;
    }
}