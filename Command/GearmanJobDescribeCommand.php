<?php

namespace Mmoreram\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Mmoreram\GearmanBundle\Command\Abstracts\AbstractGearmanCommand;
use Mmoreram\GearmanBundle\Service\GearmanClient;
use Mmoreram\GearmanBundle\Service\GearmanDescriber;

class GearmanJobDescribeCommand extends AbstractGearmanCommand
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
        $this
            ->setName('gearman:job:describe')
            ->setDescription('Describe given job')
            ->addArgument(
                'job',
                InputArgument::REQUIRED,
                'job to describe'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = $input->getArgument('job');
        $job = $this->gearmanClient->getJob($job);

        $this
            ->gearmanDescriber
            ->describeJob($output, $job);

        return 0;
    }
}