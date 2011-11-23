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
     * Return Gearman settings
     *
     * @return array Settings getted from gearmanSettings service
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
        $this->settings = $this->container->get('gearman.settings')->loadSettings();
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
            $this->loadSettings();
        }

        if (null === $this->bundles) {
            $this->bundles = array();

            foreach ($this->settings['bundles'] as $properties) {

                if ( isset($properties['active']) && (true === $properties['active']) ) {

                    if ('' !== $properties['namespace']) {
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
    public function shutdown()
    {

    }

    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @api
     */
    public function build(ContainerBuilder $container)
    {

    }
}