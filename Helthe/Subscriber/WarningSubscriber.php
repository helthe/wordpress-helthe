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
 * Subscriber that monitors for WordPress issues that should trigger a warning in PHP.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Subscriber_WarningSubscriber implements Helthe_PluginAPI_HookSubscriberInterface
{
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
            'helthe_image_editor_not_found' => 'image_editor_not_found',
            'helthe_slow_hook' => array('slow_hook', 10, 2),
            'helthe_wp_die_ajax_error' => 'wp_die_ajax_error',
            'helthe_wp_die_default_error' => 'wp_die_default_error',
            'helthe_wp_die_xml_error' => 'wp_die_xml_error',
            'helthe_wpdb_error' => array('wpdb_error', 10, 2),
            'helthe_wpdb_slow_query' => array('wpdb_slow_query', 10, 2),
            'helthe_wp_error_found' => 'wp_error_found',
        );
    }

    /**
     * Triggers an error when an image editor isn't found.
     *
     * @param array $implementations
     */
    public function image_editor_not_found(array $implementations)
    {
        $this->trigger_warning(__('No image editor could be selected.', 'helthe'));
    }

    /**
     * Triggers a warning when there's a slow hook.
     *
     * @param string $hook_name
     * @param float  $time
     */
    public function slow_hook($hook_name, $time)
    {
        $this->trigger_warning(sprintf(__('Slow WordPress hook: "%1$s" took %2$ss', 'helthe'), $hook_name, round($time, 4)));
    }

    /**
     * Triggers a warning when there's an error during an AJAX request.
     *
     * @param string $message
     */
    public function wp_die_ajax_error($message)
    {
        $this->trigger_warning(sprintf(__('An AJAX request returned the following error: %1$s', 'helthe'), $message));
    }

    /**
     * Triggers a warning when there's an error when the execution of WordPress was terminated.
     *
     * @param $message
     */
    public function wp_die_default_error($message)
    {
        $this->trigger_warning(sprintf(__('A request returned the following error: %1$s', 'helthe'), $message));
    }

    /**
     * Triggers a warning when there's an error during a XMLRPC request.
     *
     * @param string $message
     */
    public function wp_die_xml_error($message)
    {
        $this->trigger_warning(sprintf(__('A XMLRPC request returned the following error: %1$s', 'helthe'), $message));
    }

    /**
     * Triggers a warning when the plugin API returns a WP_Error.
     *
     * @param WP_Error $error
     */
    public function wp_error_found(WP_Error $error)
    {
        $this->trigger_warning($error->get_error_message());
    }

    /**
     * Triggers a warning for WPDB errors.
     *
     * @param string $error
     * @param string $query
     */
    public function wpdb_error($error, $query)
    {
        $this->trigger_warning(sprintf(__('WordPress database error: "%1$s" for query "%2$s"', 'helthe'), $error, $query));
    }

    /**
     * Triggers a warning for slow WPDB queries.
     *
     * @param string $query
     * @param float  $time
     */
    public function wpdb_slow_query($query, $time)
    {
        $this->trigger_warning(sprintf(__('Slow WordPress query: "%1$s" took %2$ss', 'helthe'), $query, round($time, 4)));
    }

    /**
     * Trigger a PHP warning.
     *
     * @param string $message
     */
    private function trigger_warning($message)
    {
        $this->plugin_api_manager->do_hook('helthe_trigger_warning_error', $message);

        trigger_error($message, E_USER_WARNING);
    }
}