<?php

namespace Mmoreram\GearmanBundle\Exceptions;

use Exception;

/**
 * GearmanBundle can't find setting value
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class SettingValueMissingException extends Exception
{

    /**
     * Construct method for Exception
     *
     * @param string     $value    Setting value not found
     * @param integer    $code     Code of exception
     * @param \Exception $previous Previos Exception
     */
    public function __construct($value, $code = 0, Exception $previous = null)
    {
        $message = 'GearmanBundle can\'t find setting value "' . $value . '"' . PHP_EOL;

        parent::__construct($message, $code, $previous);
    }
}
