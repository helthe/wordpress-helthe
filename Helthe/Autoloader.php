<?php

/*
 * This file is part of the WordPress Helthe Monitor plugin.
 *
 * (c) Carl Alexander <carlalexander@helthe.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Autoloads Helthe classes.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Autoloader
{
    /**
     * Handles autoloading of Helthe classes.
     *
     * @param string $class
     */
    public static function autoload($class)
    {
        if (0 !== strpos($class, 'Helthe')) {
            return;
        }

        if (is_file($file = dirname(__FILE__).'/../'.str_replace(array('_', "\0"), array('/', ''), $class).'.php')) {
            require $file;
        }
    }

    /**
     * Registers Helthe_Autoloader as an SPL autoloader.
     *
     * @param bool $prepend
     */
    public static function register($prepend = false)
    {
        if (version_compare(phpversion(), '5.3.0', '>=')) {
            spl_autoload_register(array(new self(), 'autoload'), true, $prepend);
        } else {
            spl_autoload_register(array(new self(), 'autoload'));
        }
    }
}
