<?php

namespace Mmoreramerino\GearmanBundle\Exceptions;

/**
 * GearmanBundle setting is not well formatted
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class SettingValueBadFormatException extends \Exception
{

    /**
     * Construct method for Exception
     *
     * @param string     $value    Setting value bad formatted
     * @param integer    $code     Code of exception
     * @param \Exception $previous Previos Exception
     */
    public function __construct($value, $code = 0, \Exception $previous = null)
    {
        $message = 'GearmanBundle setting "' . $value . '" is not well formatted' . PHP_EOL;
        parent::__construct($message, $code, $previous);
    }
}
