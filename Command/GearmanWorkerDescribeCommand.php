<?php

namespace Mmoreram\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Mmoreram\GearmanBundle\Command\Abstracts\AbstractGearmanCommand;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use Mmoreram\GearmanBundle\Service\GearmanDescriber;

class GearmanWorkerDescribeCommand extends AbstractGearmanCommand
{

    protected GearmanClient $gearmanClient;
    protected GearmanDescriber $gearmanDescriber;


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

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('gearman:worker:describe')
            ->setDescription('Describe given worker')
            ->addArgument(
                'worker',
                InputArgument::REQUIRED,
                'worker to describe'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $worker = $input->getArgument('worker');
        $worker = $this
            ->gearmanClient
            ->getWorker($worker);

        $this
            ->gearmanDescriber
            ->describeWorker(
                $output,
                $worker
            );

        return 0;
    }
}
