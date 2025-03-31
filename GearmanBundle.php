<?php

namespace Mmoreram\GearmanBundle;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Mmoreram\GearmanBundle\Driver\Gearman\Work;
use Mmoreram\GearmanBundle\Driver\Gearman\Job;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GearmanBundle extends Bundle
{
    /**
     * Boots the Bundle.
     */
    public function boot()
    {
        AnnotationRegistry::loadAnnotationClass(Work::class);
        AnnotationRegistry::loadAnnotationClass(Job::class);
    }
}