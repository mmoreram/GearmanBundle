<?php

namespace Mmoreramerino\GearmanBundle\Exceptions;

/**
 * Exception Class to wrap worker errors [E_NOTICE|E_INFO|E_ERROR|...]
 */
class GearmanWorkerExecutionErrorException extends \Exception
{

    /**
     * Context of triggered error
     * @var array $context
     */
    protected $context;

    /**
     * Construct method for Exception
     *
     * @param string     $message  Message of exception
     * @param integer    $code     Code of exception
     * @param \Exception $previous Previos Exception
     */
    public function __construct($message='', $code = 0, $file, $line, $context, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->file    = $file;
        $this->line    = $line;
        $this->context = $context;
    }

    /**
     * Get the context as string
     *
     * @return string Representation of context
     */
    public function getContextAsString()
    {
        ob_start();
        var_dump($this->context, true);
        return ob_get_clean();
    }


}

