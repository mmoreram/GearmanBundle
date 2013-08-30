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
 * GearmanBundle can't find calling method
 */
class NoCallableGearmanMethodException extends Exception
{

    /**
     * Construct method for Exception
     *
     * @param string    $calledMethod Called and not found method
     * @param integer   $code         Code of exception
     * @param Exception $previous     Previos Exception
     */
    public function __construct($calledMethod='', $code = 0, Exception $previous = null)
    {
        $message = 'GearmanBundle can\'t find "'.$calledMethod.'" method. Call "php app/console gearman:methods:list' . PHP_EOL;

        parent::__construct($message, $code, $previous);
    }
}
