<?php

namespace Mmoreramerino\GearmanBundle\Module;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mmoreramerino\GearmanBundle\Sevices\GearmanSettings;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Gearman Base Bundle
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanBaseBundle extends Bundle
{
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
