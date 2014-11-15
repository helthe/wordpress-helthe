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
 * Decorator for WPDB that checks for errors and slow queries.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Decorator_WPDB
{
    /**
     * WordPress Plugin API manager.
     *
     * @var Helthe_PluginAPI_Manager
     */
    private $plugin_api_manager;

    /**
     * Timer to track query time.
     *
     * @var Helthe_Timer
     */
    private $timer;

    /**
     * The decorated instance of WPDB.
     *
     * @var WPDB
     */
    private $wpdb;

    /**
     * Constructor.
     *
     * @param WPDB                     $wpdb
     * @param Helthe_PluginAPI_Manager $plugin_api_manager
     */
    public function __construct(WPDB $wpdb, Helthe_PluginAPI_Manager $plugin_api_manager)
    {
        $this->plugin_api_manager = $plugin_api_manager;
        $this->timer = new Helthe_Timer(false);
        $this->wpdb = $wpdb;
    }

    /**
     * Intercepts all calls to the WPDB object. If the method is tracked, performs analysis on it.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        if (!in_array($method, $this->get_tracked_methods())) {
            return call_user_func_array(array($this->wpdb, $method), $arguments);
        }

        $this->timer->start();
        $return = call_user_func_array(array($this->wpdb, $method), $arguments);
        $this->timer->stop();

        if ('' !== $this->get_last_error()) {
            $this->plugin_api_manager->do_hook('helthe_wpdb_error', $this->get_last_error(), $this->get_last_query());
        }

        if ($this->timer->get_time() > 0.05) {
            $this->plugin_api_manager->do_hook('helthe_wpdb_slow_query', $this->get_last_query(), $this->timer->get_time());
        }

        return $return;
    }

    /**
     * Get the given WPDB variable.
     *
     * @param string $variable
     *
     * @return mixed
     */
    public function __get($variable)
    {
        return $this->wpdb->$variable;
    }

    /**
     * Sets the value of the given WPDB variable.
     *
     * @param string $variable
     * @param mixed  $value
     */
    public function __set($variable, $value)
    {
        $this->wpdb->$variable = $value;
    }

    /**
     * Checks if the given WPDB variable is set.
     *
     * @param string $variable
     *
     * @return bool
     */
    public function __isset($variable)
    {
        return isset($this->wpdb->$variable);
    }

    /**
     * Unsets the given WPDB variable.
     *
     * @param string $variable
     */
    public function __unset($variable)
    {
        unset($this->wpdb->$variable);
    }

    /**
     * Get the last error recorded by WPDB.
     *
     * @return string
     */
    private function get_last_error()
    {
        return $this->wpdb->last_error;
    }

    /**
     * Get the last query that WPDB did.
     *
     * @return string
     */
    private function get_last_query()
    {
        return $this->wpdb->last_query;
    }

    /**
     * Get the WPDB methods tracked by the decorator.
     *
     * @return array
     */
    public function get_tracked_methods()
    {
        $methods = array('delete', 'get_col', 'get_results', 'get_row', 'get_var', 'query', 'update');

        return $this->plugin_api_manager->apply_hooks('helthe_wpdb_tracked_methods', $methods);
    }
}