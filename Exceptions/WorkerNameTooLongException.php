<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Exceptions;

use Mmoreram\GearmanBundle\Exceptions\Abstracts\AbstractGearmanException;
use Exception;

/**
 * GearmanBundle can't find worker specified as Gearman format Exception
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
        $message = 'The function name + unique id cannot exceed 114 bytes. You can change workers name or set a shortly unique key';

        parent::__construct($message, $code, $previous);
    }
}
