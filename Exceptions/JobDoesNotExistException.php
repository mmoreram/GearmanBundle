<?php

namespace Ulabox\GearmanBundle\Exceptions;

/**
 * GearmanBundle can't find job specified as Gearman format Exception
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
class JobDoesNotExistException extends \Exception
{
    
    /**
     * Construct method for Exception
     *
     * @param string $job Job name to be shown in Exception
     * @param integer $code
     * @param \Exception $previous 
     */
    public function __construct($job, $code = 0, \Exception $previous = null) {
        
        $message = 'GearmanBundle can\'t find job with name ' . $job . PHP_EOL;
        parent::__construct($message, $code, $previous);
    }
}
