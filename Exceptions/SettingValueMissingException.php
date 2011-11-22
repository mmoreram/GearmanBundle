<?php

namespace Ulabox\GearmanBundle\Exceptions;

/**
 * GearmanBundle can't find setting value
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
class SettingValueMissingException extends \Exception
{
    
    /**
     * Construct method for Exception
     *
     * @param string $value Setting value not found in settings file
     * @param integer $code
     * @param \Exception $previous 
     */
    public function __construct($value, $code = 0, \Exception $previous = null) {
        
        $message = 'GearmanBundle can\'t find setting value "' . $value . '"' . PHP_EOL;
        parent::__construct($message, $code, $previous);
    }
}
