<?php

namespace Mmoreramerino\GearmanBundle\Exceptions;

/**
 * GearmanBundle can't find settings into specified path Exception
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class NoSettingsFileExistsException extends \Exception
{

    /**
     * Construct method for Exception
     *
     * @param string     $path     Path of setting file not found
     * @param integer    $code     Code of exception
     * @param \Exception $previous Previos Exception
     */
    public function __construct($path, $code = 0, \Exception $previous = null)
    {
        $message = 'GearmanBundle can\'t find settings file in path "' . $path . '"' . PHP_EOL;
        parent::__construct($message, $code, $previous);
    }
}
