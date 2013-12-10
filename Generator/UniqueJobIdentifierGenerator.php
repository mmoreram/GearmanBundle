<?php

/**
 * Gearman Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Generator;

/**
 * Job Unique Key generator
 *
 * @author Marc Morera <yuhu@mmoreram.com>
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
     * Even some parameters are not used, are passed to allow user overwrite method
     *
     * @param string $name   A GermanBundle registered function to be executed
     * @param string $params Parameters to send to task as string
     * @param string $unique unique ID used to identify a particular task
     * @param string $method Method to perform
     *
     * @return string Generated Unique Key
     */
    public function generateUniqueKey($name, $params, $unique, $method)
    {
        return  ( !$unique && $this->generateUniqueKey )
                ? md5($name . $params)
                : $unique;
    }
}