<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Exceptions;

use Exception;

/**
 * GearmanBundle can't find worker specified as Gearman format Exception
 */
class WorkerDoesNotExistException extends Exception
{

    /**
     * Construct method for Exception
     *
     * @param string    $worker   Worker name to be shown in Exception
     * @param integer   $code     Code of exception
     * @param Exception $previous Previos Exception
     */
    public function __construct($worker, $code = 0, Exception $previous = null)
    {
        $message = 'GearmanBundle can\'t find worker with name ' . $worker . PHP_EOL;

        parent::__construct($message, $code, $previous);
    }
}
