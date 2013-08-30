<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

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
        $workers = $this->getContainer()->get('gearman')->getWorkers();

        if (is_array($workers)) {

            $it = 1;

            foreach ($workers as $worker) {
                
                $output->writeln('<comment>    @Worker:  </comment><info>'.$worker['className'].'</info>');
                $output->writeln('<comment>    callablename:  </comment><info>'.$worker['callableName'].'</info>');
                $output->writeln('<comment>    Jobs:</comment>');
                foreach ($worker['jobs'] as $job) {
                    $output->writeln('<comment>      - #'.$it++.'</comment>');
                    $output->writeln('<comment>          name: '.$job['methodName'].'</comment>');
                    $output->writeln('<comment>          callablename:</comment><info> '.$job['realCallableName'].'</info>');
                }
                $output->writeln('');
            }
        }
    }
}