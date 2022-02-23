<?php

/**
 * Gearman Bundle for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Mmoreram\GearmanBundle;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Gearman Bundle
 *
 * @since 2.3.1
 */
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
