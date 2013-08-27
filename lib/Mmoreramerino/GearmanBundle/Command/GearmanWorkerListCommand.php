<?php

namespace Mmoreramerino\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Mmoreramerino\GearmanBundle\Module\WorkerClass;

/**
 * Gearman Job List Command class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanWorkerListCommand extends ContainerAwareCommand
{
    /**
     * Console Command configuration
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('gearman:worker:list')
             ->setDescription('List all Gearman Workers and their Jobs');
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
        /** @var WorkerClass[] $workers  */
        $workers = $this->getContainer()->get('gearman')->getWorkers();
        if (is_array($workers)) {
            $it = 1;
            foreach ($workers as $worker) {
                $output->writeln('<comment>    @Worker:  </comment><info>'.$worker->getClassName().'</info>');
                $output->writeln('<comment>    callablename:  </comment><info>'.$worker->getCallableName().'</info>');
                $output->writeln('<comment>    Jobs:</comment>');
                foreach ($worker->getJobCollection() as $job) {
                    $output->writeln('<comment>      - #'.$it++.'</comment>');
                    $output->writeln('<comment>          name: '.$job->getMethodName().'</comment>');
                    $output->writeln('<comment>          callablename:</comment><info> '.$job->getRealCallableName().'</info>');
                }
                $output->writeln('');
            }
        }
    }
}