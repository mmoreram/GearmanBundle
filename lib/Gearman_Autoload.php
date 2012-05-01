<?php

/**
 * Test autoload
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

class Gearman_Autoloader
{
    /**
    * Registers Gearman_Autoloader as an SPL autoloader.
    */
    static public function register()
    {

        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
    * Handles autoloading of classes.
    *
    * @param string $class A class name.
    *
    * @return boolean Returns true if the class has been loaded
    */
    static public function autoload($class)
    {

        if (0 !== strpos($class, 'Mmoreramerino')) {

            return;
        }

        $file = dirname(__FILE__).'/'.str_replace(array('\\', "\0"), array('/', ''), $class).'.php';

        if (is_file($file)) {

            require $file;
        }
    }
}