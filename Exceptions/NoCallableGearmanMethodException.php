<?php

namespace Mmoreramerino\GearmanBundle\Exceptions;

/**
 * GearmanBundle can't find calling method
 *
 * @author Marc Morera <marc@ulabox.com>
 */
class NoCallableGearmanMethodException extends \Exception
{

    /**
     * Construct method for Exception
     *
     * @param string     $calledMethod Called and not found method
     * @param integer    $code         Code of exception
     * @param \Exception $previous     Previos Exception
     */
    public function __construct($calledMethod='', $code = 0, \Exception $previous = null)
    {
        $message = 'GearmanBundle can\'t find "'.$calledMethod.'" method. Call "php app/console gearman:methods:list' . PHP_EOL;
        parent::__construct($message, $code, $previous);
    }
}
