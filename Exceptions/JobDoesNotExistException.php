<?php

namespace Mmoreram\GearmanBundle\Exceptions;

use Exceptions\GearmanExceptionInterface;

class JobDoesNotExistException extends \InvalidArgumentException implements GearmanExceptionInterface
{
}