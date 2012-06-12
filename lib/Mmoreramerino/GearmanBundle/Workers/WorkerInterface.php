<?php

namespace Mmoreramerino\GearmanBundle\Workers;

use Mmoreramerino\GearmanBundle\Driver\Gearman;

interface WorkerInterface {

    /**
     * Default Error Handler
     *
     * @param integer $code     Code
     * @param string  $message  Message
     * @param string  $filename Filename
     * @param string  $line     Line
     * @param array   $context  Context
     *
     * @return string Error Message
     */
    public function errorHandler($code, $message, $filename, $line, $context = array());

    /**
     * Default Exception Handler
     *
     * @param \Exception $exception Exception
     */
    public function exceptionHandler($exception);

}

