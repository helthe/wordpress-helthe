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
 * Subscriber that inspects every hook in WordPress for WP_Error objects. It does so by attaching a verification
 * callback for each hook called while WordPress is running.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Subscriber_WPErrorSubscriber implements Helthe_PluginAPI_HookSubscriberInterface
{
    /**
     * Array of all the WordPress hooks we have interacted with.
     *
     * @var array
     */
    private $hooks;

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
        $this->hooks = array();
        $this->plugin_api_manager = $plugin_api_manager;
    }

    /**
     * {@inheritdoc}
     */
    public static function get_hooks()
    {
        return array(
            'all' => 'add_hook'
        );
    }

    /**
     * Add a new hook to the current hook being executed to check for errors.
     */
    public function add_hook()
    {
        $hook_name = $this->plugin_api_manager->get_current_hook();

        // Don't check our hook since it's always a WP_Error
        if (in_array($hook_name, $this->hooks) || $hook_name === 'helthe_wp_error_found') {
            return;
        }

        // This is a lot faster than doing a has_hook check
        $this->hooks[] = $hook_name;

        $this->plugin_api_manager->add_hook($hook_name, array($this, 'check_for_error'), 9999);
    }

    /**
     * Checks if the given value is an instance of WP_Error.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function check_for_error($value = null)
    {
        if ($value instanceof WP_Error) {
            $this->plugin_api_manager->do_hook('helthe_wp_error_found', $value);
        }

        return $value;
    }
}