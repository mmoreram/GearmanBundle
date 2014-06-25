<?php

/**
 * Gearman Bundle for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Mmoreram\GearmanBundle\Generator;

use Mmoreram\GearmanBundle\Exceptions\WorkerNameTooLongException;

/**
 * Job Unique Key generator
 *
 * @see https://github.com/mmoreram/GearmanBundle/issues/66
 *
 * @since 2.3.1
 */
class UniqueJobIdentifierGenerator
{
    /**
     * @var string
     *
     * Generate unique key
     */
    protected $generateUniqueKey;

    /**
     * Construct method
     *
     * @param string $generateUniqueKey Generate unique key
     */
    public function __construct($generateUniqueKey)
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
     * @param string $name   A GermanBundle registered function to be executed
     * @param string $params Parameters to send to task as string
     * @param string $unique unique ID used to identify a particular task
     * @param string $method Method to perform
     *
     * @return string Generated Unique Key
     *
     * @throws WorkerNameTooLongException If name is too large
     *
     * @api
     */
    public function generateUniqueKey($name, $params, $unique, $method)
    {
        $unique = !$unique && $this->generateUniqueKey
            ? md5($name . $params)
            : $unique;

        if (strlen($name . $unique) > 114) {

            throw new WorkerNameTooLongException;
        }

        return $unique;
    }
}
