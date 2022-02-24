<?php

namespace Mmoreram\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Mmoreram\GearmanBundle\Command\Abstracts\AbstractGearmanCommand;
use Mmoreram\GearmanBundle\Service\GearmanCacheWrapper;

class GearmanCacheWarmupCommand extends AbstractGearmanCommand
{
    protected GearmanCacheWrapper $gearmanCacheWrapper;

    public function setGearmanCacheWrapper(GearmanCacheWrapper $gearmanCacheWrapper): self
    {
        $this->gearmanCacheWrapper = $gearmanCacheWrapper;

        return $this;
    }

    protected function configure()
    {
        $this
            ->setName('gearman:cache:warmup')
            ->setAliases([
                'cache:gearman:warmup',
            ])
            ->setDescription('Warms up gearman cache data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (
            !$input->getOption('quiet')
        ) {
            $kernelEnvironment = $this
                ->kernel
                ->getEnvironment();

            $output->writeln('Warming the cache for the ' . $kernelEnvironment . ' environment');
        }

        $this
            ->gearmanCacheWrapper
            ->warmup('');

        return 0;
    }
}