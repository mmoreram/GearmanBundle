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

namespace Mmoreram\GearmanBundle\Exceptions;

use Exception;

use Mmoreram\GearmanBundle\Exceptions\Abstracts\AbstractGearmanException;

/**
 * GearmanBundle can't find worker specified as Gearman format Exception
 *
 * @since 2.3.3
 */
class WorkerNameTooLongException extends AbstractGearmanException
{
    /**
     * Construction method
     *
     * @param string    $message  Message
     * @param int       $code     Code
     * @param Exception $previous Previous
     */
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        $message = 'The function name + unique id cannot exceed 114 bytes.
                    You can change workers name or set a shortly unique key';

        parent::__construct($message, $code, $previous);
    }
}
