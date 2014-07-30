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

namespace Mmoreram\GearmanBundle\Command\Abstracts;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AbstractGearmanCommand
 *
 * @since 2.3.1
 */
abstract class AbstractGearmanCommand extends Command
{
    /**
     * @var KernelInterface
     *
     * Kernel
     */
    protected $kernel;

    /**
     * Set kernel
     *
     * @param KernelInterface $kernel Kernel
     *
     * @return AbstractGearmanCommand self Object
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        return $this;
    }
}
