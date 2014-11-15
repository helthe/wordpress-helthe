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
 * HookSubscriberInterface is used by an object that needs to subscribe to WordPress hooks.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
interface Helthe_PluginAPI_HookSubscriberInterface
{
    /**
     * Returns an array of hooks that the object needs to be subscribed to.
     *
     * The array key is the name of the hook. The value can be:
     *
     *  * The method name
     *  * An array with the method name and priority
     *  * An array with the method name, priority and number of accepted arguments
     *
     * For instance:
     *
     *  * array('hook_name' => 'method_name')
     *  * array('hook_name' => array('method_name', $priority))
     *  * array('hook_name' => array('method_name', $priority, $accepted_args))
     *
     * @return array
     */
    public static function get_hooks();
}
 