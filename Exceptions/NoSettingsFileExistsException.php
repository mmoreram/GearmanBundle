<?php

namespace Mmoreramerino\GearmanBundle\Exceptions;

/**
 * GearmanBundle can't find settings into specified path Exception
 * 
 * @author Marc Morera <marc@ulabox.com>
 */
class NoSettingsFileExistsException extends \Exception
{
    
    /**
     * Construct method for Exception
     *
     * @param string $path
     * @param integer $code
     * @param \Exception $previous 
     */
    public function __construct($path, $code = 0, \Exception $previous = null) {
        
        $message = 'GearmanBundle can\'t find settings file in path "' . $path . '"' . PHP_EOL;
        parent::__construct($message, $code, $previous);
    }
}
