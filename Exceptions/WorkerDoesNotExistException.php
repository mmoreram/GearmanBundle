<?php

namespace Mmoreram\GearmanBundle\Exceptions;

use Exceptions\GearmanExceptionInterface;

class WorkerDoesNotExistException extends \InvalidArgumentException implements GearmanExceptionInterface
{
}