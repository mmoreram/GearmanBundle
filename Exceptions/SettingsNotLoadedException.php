<?php

namespace Ulabox\GearmanBundle\Exceptions;

/**
 * GearmanBundle has not already load settings file Exception
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
class SettingsNotLoadedException extends \Exception
{
    
    /**
     * Construct method for Exception
     *
     * @param string $message
     * @param integer $code
     * @param \Exception $previous 
     */
    public function __construct($job, $code = 0, \Exception $previous = null) {
        
        $message = 'GearmanBundle has not already loaded settings from file. Be sure to call load() before use this call.' . PHP_EOL;
        parent::__construct($message, $code, $previous);
    }
}
