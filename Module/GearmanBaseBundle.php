<?php

namespace Mmoreramerino\GearmanBundle\Module;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mmoreramerino\GearmanBundle\Sevices\GearmanSettings;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Gearman Base Bundle
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
class GearmanBaseBundle extends Bundle
{
    /**
     * Settings defined into settings file
     * 
     * @var Array
     */
    private $settings = null;
    
    
    /**
     * Bundles available to perform search setted in bundles.yml file
     * 
     * @var Array
     */
    private $bundles = null;
    
    
    /**
     * Return Gearman settings, previously loaded by method load()
     * If settings are not loaded, a SettingsNotLoadedException Exception is thrown
     *
     * @return array Settings getted from gearmanSettings service
     */
    public function getSettings()
    {
        $this->settings = $this->container->get('gearman.settings')->getSettings();
        
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
        $this->settings = $this->container->get('gearman.settings')->loadSettings($settingsPath);
        
        return $this->settings;
    }
    
    
    /**
     * Return Gearman bundle settings, previously loaded by method load()
     * If settings are not loaded, a SettingsNotLoadedException Exception is thrown
     *
     * @return array Bundles that gearman will be able to search annotations
     */
    public function getParseableBundles()
    {
        if (null === $this->settings) {
            throw new SettingsNotLoadedException();
        }
        
        if (null === $this->bundles) {
            $this->bundles = array();
            
            foreach ($this->settings['bundles'] as $properties) {

                if ( isset($properties['active']) && (true === $properties['active']) ) {

                    if('' !== $properties['namespace']) {
                        $this->bundles[] = $properties['namespace'];
                    }
                }
            }
        }
        
        return $this->bundles;
    }
    
    /**
     * Shutdowns the Bundle.
     *
     * @api
     */
    function shutdown(){}

    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @api
     */
    function build(ContainerBuilder $container){}
}