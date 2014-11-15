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
 * Helthe_PluginAPI_Manager handles all interaction with the WordPress Plugin API.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_PluginAPI_Manager
{
    /**
     * Adds a callback to a specific hook of the WordPress plugin API.
     *
     * @uses add_filter()
     *
     * @param string $name
     * @param mixed  $callback
     * @param int    $priority
     * @param int    $accepted_args
     */
    public function add_hook($name, $callback, $priority = 10, $accepted_args = 1)
    {
        add_filter($name, $callback, $priority, $accepted_args);
    }

    /**
     * Applies all the changes associated with the given hook to the given value.
     *
     * @uses apply_filters()
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function apply_hooks()
    {
        return call_user_func_array('apply_filters', func_get_args());
    }

    /**
     * Executes all the functions associated with the given hook.
     *
     * @uses do_action()
     *
     * @param string $name
     */
    public function do_hook()
    {
        return call_user_func_array('do_action', func_get_args());
    }

    /**
     * Get the name of the most recent hook that WordPress has executed or is executing.
     *
     * @uses current_filter()
     *
     * @return string
     */
    public function get_current_hook()
    {
        return current_filter();
    }

    /**
     * Checks if the given hook has the given callback. The priority of the callback will be returned or false. If
     * no callback is given will return true or false if there's any callbacks registered to the hook.
     *
     * @uses has_filter()
     *
     * @param string $name
     * @param mixed  $callback
     *
     * @return bool|int
     */
    public function has_hook($name, $callback = false)
    {
        return has_filter($name, $callback);
    }

    /**
     * Registers an object with the WordPress Plugin API.
     *
     * @param mixed $object
     */
    public function register($object)
    {
        if ($object instanceof Helthe_PluginAPI_HookSubscriberInterface) {
            $this->register_hooks($object);
        }
    }

    /**
     * Register a filter hook subscriber with a specific hook.
     *
     * @param Helthe_PluginAPI_HookSubscriberInterface $subscriber
     * @param string                                   $name
     * @param mixed                                    $parameters
     */
    private function register_hook(Helthe_PluginAPI_HookSubscriberInterface $subscriber, $name, $parameters)
    {
        if (is_string($parameters)) {
            $this->add_hook($name, array($subscriber, $parameters));
        } elseif (is_array($parameters) && isset($parameters[0])) {
            $this->add_hook($name, array($subscriber, $parameters[0]), isset($parameters[1]) ? $parameters[1] : 10, isset($parameters[2]) ? $parameters[2] : 1);
        }
    }

    /**
     * Registers a hook subscriber with all its hooks.
     *
     * @param Helthe_PluginAPI_HookSubscriberInterface $subscriber
     */
    private function register_hooks(Helthe_PluginAPI_HookSubscriberInterface $subscriber)
    {
        foreach ($subscriber->get_hooks() as $name => $parameters) {
            $this->register_hook($subscriber, $name, $parameters);
        }
    }
}