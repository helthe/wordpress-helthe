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
 * Subscriber that intercepts all calls to wp_die.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Subscriber_WPDieSubscriber implements Helthe_PluginAPI_HookSubscriberInterface
{
    /**
     * Old AJAX handler.
     *
     * @var mixed
     */
    private $old_ajax_handler;

    /**
     * Old Default handler.
     *
     * @var mixed
     */
    private $old_default_handler;

    /**
     * Old XMLRPC handler.
     *
     * @var mixed
     */
    private $old_xml_handler;

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
     * {@inheritdoc}
     */
    public static function get_hooks()
    {
        return array(
            'wp_die_ajax_handler'   => array('register_ajax_handler', 9999),
            'wp_die_handler'        => array('register_default_handler', 9999),
            'wp_die_xmlrpc_handler' => array('register_xml_handler', 9999)
        );
    }

    /**
     * Handles when the execution of WordPress is terminated during an AJAX request.
     *
     * @param string $message
     */
    public function handle_ajax($message = '')
    {
        // Only trigger an error there is an actual message.
        if (!empty($message) && !is_numeric($message) && !$this->is_json($message)) {
            $this->plugin_api_manager->do_hook('helthe_wp_die_ajax_error', $message);
        }

        // Call the registered handler
        if (is_callable($this->old_ajax_handler)) {
            call_user_func($this->old_ajax_handler, $message);
        }
    }

    /**
     * Handles when the execution of WordPress is terminated.
     *
     * @param mixed  $message
     * @param string $title
     * @param array  $args
     */
    public function handle_default($message = '', $title = '', $args = array())
    {
        if ($message instanceof WP_Error) {
            $this->plugin_api_manager->do_hook('helthe_wp_die_default_error', $message->get_error_message());
        }

        // Call the registered handler
        if (is_callable($this->old_default_handler)) {
            call_user_func($this->old_default_handler, $message, $title, $args);
        }
    }

    /**
     *  Handles when the execution of WordPress is terminated during a XMLRPC request.
     *
     * @param mixed  $message
     * @param string $title
     * @param array  $args
     */
    public function handle_xml($message = '', $title = '', $args = array())
    {
        // Only trigger an error there is an actual message.
        if (!empty($message)) {
            $this->plugin_api_manager->do_hook('helthe_wp_die_xml_error', $message);
        }

        // Call the registered handler
        if (is_callable($this->old_xml_handler)) {
            call_user_func($this->old_xml_handler, $message, $title, $args);
        }
    }

    /**
     * Registers our AJAX handler and saves the current one.
     *
     * @param mixed $handler
     *
     * @return array
     */
    public function register_ajax_handler($handler)
    {
        $this->old_ajax_handler = $handler;

        return array($this, 'handle_ajax');
    }

    /**
     * Registers our default handler and saves the current one.
     *
     * @param mixed $handler
     *
     * @return array
     */
    public function register_default_handler($handler)
    {
        $this->old_default_handler = $handler;

        return array($this, 'handle_default');
    }

    /**
     * Registers our XMLRPC handler and saves the current one.
     *
     * @param mixed $handler
     *
     * @return array
     */
    public function register_xml_handler($handler)
    {
        $this->old_xml_handler = $handler;

        return array($this, 'handle_xml');
    }

    /**
     * Checks if the string is formatted in JSON.
     *
     * @param string $string
     *
     * @return bool
     */
    private function is_json($string)
    {
        return is_array(json_decode($string, true));
    }
}