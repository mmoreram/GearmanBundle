<?php

namespace Mmoreram\GearmanBundle\Command\Abstracts;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\KernelInterface;


abstract class AbstractGearmanCommand extends Command
{
    protected KernelInterface $kernel;

    public function setKernel(KernelInterface $kernel): self
    {
        $this->kernel = $kernel;

        return $this;
    }
}
