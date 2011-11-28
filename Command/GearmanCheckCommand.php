<?php

namespace Mmoreramerino\GearmanBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Mmoreramerino\GearmanBundle\Service\GearmanSettings;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Mmoreramerino\GearmanBundle\Exceptions\GearmanNotInstalledException;
use Mmoreramerino\GearmanBundle\Exceptions\NoSettingsFileExistsException;

/**
 * Checks gearman environment
 *
 * @author Marc Morera <marc@ulabox.com>
 */
class GearmanCheckCommand extends ContainerAwareCommand
{
    /**
     * Console Command configuration
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('gearman:check')
             ->setDescription('Checks gearman environment');
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
        if (!in_array('gearman', get_loaded_extensions())) {
            throw new GearmanNotInstalledException;
        } else {
            $output->writeln('<comment>* Checking gearman extension...</comment>');
        }

        $gearmanSettings = $this->getContainer()->get('gearman.settings');
        if (!$gearmanSettings->existsSettings()) {
            throw new NoSettingsFileExistsException($this->getFilePath());
        } else {
            $output->writeln('<comment>* Checking gearman settings file...</comment>');
        }

        $output->writeln('<comment>Gearman is succesfuly installed</comment>');
    }
}