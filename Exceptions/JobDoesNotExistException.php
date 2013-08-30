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
 * GearmanBundle can't find job specified as Gearman format Exception
 */
class JobDoesNotExistException extends Exception
{

    /**
     * Construct method for Exception
     *
     * @param string    $job      Job name to be shown in Exception
     * @param integer   $code     Code of exception
     * @param Exception $previous Previos Exception
     */
    public function __construct($job, $code = 0, Exception $previous = null)
    {
        $message = 'GearmanBundle can\'t find job with name ' . $job . PHP_EOL;

        parent::__construct($message, $code, $previous);
    }
}
