<?php

namespace Mmoreram\GearmanBundle;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GearmanBundle extends Bundle
{
    /**
     * Boots the Bundle.
     */
    public function boot()
    {
        $kernel = $this->container->get('kernel');

        AnnotationRegistry::registerFile(
            $kernel
                ->locateResource("@GearmanBundle/Driver/Gearman/Work.php")
        );

        AnnotationRegistry::registerFile(
            $kernel
                ->locateResource("@GearmanBundle/Driver/Gearman/Job.php")
        );
    }
}