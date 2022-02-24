<?php

namespace Mmoreram\GearmanBundle\Command\Util;

use Symfony\Component\Console\Output\OutputInterface;

interface GearmanOutputAwareInterface
{
    public function setOutput(OutputInterface $output);
}
