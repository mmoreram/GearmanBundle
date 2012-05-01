<?php

namespace Mmoreramerino\GearmanBundle\Service;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\DependencyInjection\ContainerAware;
use Mmoreramerino\GearmanBundle\Exceptions\NoSettingsFileExistsException;

/**
 * Class GearmanSettings
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanSettings extends ContainerAware
{
    /**
     * Config file path
     *
     * @var string
     */
    private $filepath = null;

    /**
     * Settings defined into settings file
     *
     * @var Array
     */
    private $settings = null;


    /**
     * Return Gearman settings, previously loaded by method load()
     *
     * @return array Settings getted from file
     */
    public function getSettings()
    {

        return $this->loadSettings();
    }

    /**
     * Get yaml file and load all settings for Gearman engine
     *
     * @return array Settings
     */
    public function loadSettings()
    {
        if (!$this->existsSettings()) {
            throw new NoSettingsFileExistsException($this->getFilePath());
        }

        $yaml = new Parser();
        $this->settings = $yaml->parse(file_get_contents($this->getFilePath()));

        return $this->settings;
    }

    /**
     * Return if exists settings file
     *
     * @return boolean
     */
    public function existsSettings()
    {

        return file_exists($this->getFilePath());
    }

    /**
     * Get settings file config from parameters and construct path
     *
     * @return string
     */
    public function getFilePath()
    {
        if (null === $this->filepath) {
            $rootDir = $this->container->get('kernel')->getRootDir();
            $this->filepath = $rootDir . $this->container->getParameter('config.path') . $this->container->get('kernel')->getEnvironment().'.yml';
        }

        return $this->filepath;
    }
}
