<?php

namespace Mmoreram\GearmanBundle\Exceptions;

use Exception;

/**
 * GearmanBundle can't find job specified as Gearman format Exception
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class JobDoesNotExistException extends Exception
{

    /**
     * Construct method for Exception
     *
     * @param string     $job      Job name to be shown in Exception
     * @param integer    $code     Code of exception
     * @param \Exception $previous Previos Exception
     */
    public function __construct($job, $code = 0, Exception $previous = null)
    {
        $message = 'GearmanBundle can\'t find job with name ' . $job . PHP_EOL;

        parent::__construct($message, $code, $previous);
    }
}
