<?php

namespace Mmoreramerino\GearmanBundle\Exceptions;

/**
 * GearmanBundle can't find job specified as Gearman format Exception
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class GearmanNotInstalledException extends \Exception
{

    /**
     * Construct method for Exception
     *
     * @param string     $message  Message of exception
     * @param integer    $code     Code of exception
     * @param \Exception $previous Previos Exception
     */
    public function __construct($message='', $code = 0, \Exception $previous = null)
    {
        $message = 'GearmanBundle can\'t find "Gearman php extension". Be sure is actived. If is not installed yet, you can install it using PECL ( sudo pecl install gearman )' . PHP_EOL;
        parent::__construct($message, $code, $previous);
    }
}
