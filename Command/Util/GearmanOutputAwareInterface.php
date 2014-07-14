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
 * @author Dominic Grostate <codekestrel@googlemail.com>
 */

namespace Mmoreram\GearmanBundle\Command\Util;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface GearmanOutputAwareInterface
 *
 * @since 2.4.2
 */
interface GearmanOutputAwareInterface
{
    /**
     * Set the output
     *
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output);
}
