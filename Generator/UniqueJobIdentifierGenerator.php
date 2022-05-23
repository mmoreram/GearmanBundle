<?php

namespace Mmoreram\GearmanBundle\Generator;

use Mmoreram\GearmanBundle\Exceptions\WorkerNameTooLongException;

class UniqueJobIdentifierGenerator
{
    protected bool $generateUniqueKey;

    public function __construct(bool $generateUniqueKey)
    {
        $this->generateUniqueKey = $generateUniqueKey;
    }

    /**
     * Generate unique key if generateUniqueKey is enabled
     *
     * Even some parameters are not used, are passed to allow user overwrite
     * method
     *
     * Also, if name and unique value exceeds 114 bytes, an exception is thrown
     *
     * @throws WorkerNameTooLongException If name is too large
     *
     * @api
     */
    public function generateUniqueKey(string $name, string $params, ?string $unique, ?string $method = null): ?string
    {
        $unique = !$unique && $this->generateUniqueKey
            ? md5($name . $params)
            : $unique;

        if (strlen($name . $unique) > 114) {
            throw new WorkerNameTooLongException();
        }

        return $unique;
    }
}
