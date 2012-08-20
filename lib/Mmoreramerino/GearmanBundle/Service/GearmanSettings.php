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

        return $this->settings;
    }

    /**
     * @param Array $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }
}
