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
 * Helthe error handler. Handles both errors and exceptions so that errors can be handled silently.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_ErrorHandler
{
    /**
     * WordPress Plugin API manager.
     *
     * @var Helthe_PluginAPI_Manager
     */
    private $plugin_api_manager;

    /**
     * @var string
     */
    private $reserved_memory;

    /**
     * Register the error handler.
     *
     * @param Helthe_PluginAPI_Manager $plugin_api_manager
     * @param int                      $level
     *
     * @return Helthe_ErrorHandler
     */
    public static function register(Helthe_PluginAPI_Manager $plugin_api_manager, $level = null)
    {
        $handler = new self($plugin_api_manager);

        if (null !== $level) {
            error_reporting($level);
        }

        set_error_handler(array($handler, 'handle_error'), $level);
        set_exception_handler(array($handler, 'handle_exception'));
        register_shutdown_function(array($handler, 'handle_fatal'));

        return $handler;
    }

    /**
     * Constructor.
     *
     * @param Helthe_PluginAPI_Manager $plugin_api_manager
     */
    public function __construct(Helthe_PluginAPI_Manager $plugin_api_manager)
    {
        $this->plugin_api_manager = $plugin_api_manager;
        $this->reserved_memory = str_repeat('x', 10240);
    }

    /**
     * Handles errors.
     *
     * @param int     $level
     * @param string  $message
     * @param string  $file
     * @param int     $line
     *
     * @return bool
     */
    public function handle_error($level, $message, $file = 'unknown', $line = 0)
    {
        $this->plugin_api_manager->do_hook('helthe_handle_error', $message, $level, $file, $line);

        $this->handle_exception(new ErrorException($message, 0, $level, $file, $line));

        return false;
    }

    /**
     * Handles exceptions.
     *
     * @param Exception $exception
     */
    public function handle_exception(Exception $exception)
    {
        $this->plugin_api_manager->do_hook('helthe_handle_exception', $exception);
    }

    /**
     * Handles fatal errors.
     */
    public function handle_fatal()
    {
        if (null === $error = error_get_last()) {
            return;
        }

        unset($this->reservedMemory);
        $level = $error['type'];

        // Only handle PHP fatal errors
        if (!in_array($level, array(E_ERROR, E_USER_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE))) {
            return;
        }

        $exception = new Helthe_Exception_FatalErrorException($error['message'], 0, $level, $error['file'], $error['line']);

        $this->plugin_api_manager->do_hook('helthe_handle_fatal', $exception);

        $this->handle_exception($exception);
    }
}
