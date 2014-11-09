<?php

/*
 * This file is part of the WordPress Helthe plugin.
 *
 * (c) Helthe
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Collects all recorded exceptions.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Exception_Collector
{
    /**
     * All collected errors.
     *
     * @var array
     */
    private $exceptions = array();

    /**
     * WordPress Plugin API manager.
     *
     * @var Helthe_PluginAPI_Manager
     */
    private $plugin_api_manager;

    /**
     * Constructor.
     *
     * @param Helthe_PluginAPI_Manager $plugin_api_manager
     */
    public function __construct(Helthe_PluginAPI_Manager $plugin_api_manager)
    {
        $this->plugin_api_manager = $plugin_api_manager;
    }

    /**
     * Collect an exception.
     *
     * @param Exception $exception
     */
    public function collect_exception(Exception $exception)
    {
        $this->plugin_api_manager->do_hook('helthe_collect_exception', $exception);

        $this->exceptions[] = $exception;
    }

    /**
     * Get all the collected exceptions.
     *
     * @return Exception[]
     */
    public function get_exceptions()
    {
        return $this->exceptions;
    }
}