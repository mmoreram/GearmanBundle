<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Gearman Bundle
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanBundle extends Bundle
{

    /**
     * Boots the Bundle.
     */
    public function boot()
    {
        $kernel = $this->container->get('kernel');

        AnnotationRegistry::registerFile($kernel
            ->locateResource("@GearmanBundle/Driver/Gearman/Work.php")
        );

        AnnotationRegistry::registerFile($kernel
            ->locateResource("@GearmanBundle/Driver/Gearman/Job.php")
        );
    }
}
