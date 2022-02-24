<?php

namespace Mmoreram\GearmanBundle\Exceptions;

use Exceptions\GearmanExceptionInterface;

class WorkerNameTooLongException extends \LengthException implements GearmanExceptionInterface
{

    public function __construct(?string $message = null, int $code = 0, \Exception $previous = null)
    {
        parent::__construct(
            $message ?? 'The function name + unique id cannot exceed 114 bytes.
                    You can change workers name or set a shortly unique key',
            $code,
            $previous
        );
    }
}