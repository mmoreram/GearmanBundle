<?php

namespace Ulabox\GearmanBundle\Service;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\DependencyInjection\ContainerAware;
use Ulabox\GearmanBundle\Exceptions\SettingsNotLoadedException;
use Ulabox\GearmanBundle\Exceptions\NoSettingsFileExistsException;

/**
 * Class GearmanSettings
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
class GearmanSettings extends ContainerAware
{
    /**
     * Settings defined into settings file
     * 
     * @var Array
     */
    private $settings = null;
    
    /**
     * Return Gearman settings, previously loaded by method load()
     * If settings are not loaded, a SettingsNotLoadedException Exception is thrown
     *
     * @return array Settings getted from file
     */
    public function getSettings()
    {
        if (null === $this->settings) {
            throw new SettingsNotLoadedException();
        }
        
        return $this->settings;
    }
    
    /**
     * Get yaml file and load all settings for Gearman engine
     *
     * @param type $settingsPath Resource path to get settings
     * @return array Settings
     */
    public function loadSettings($settingsPath)
    {
        
        if (!file_exists($settingsPath)) {
            throw new NoSettingsFileExistsException($settingsPath);
        }
        
        $yaml = new Parser();
        $this->settings = $yaml->parse(file_get_contents($settingsPath));
        
        return $this->settings;
    }
}
