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
 * Helthe Plugin.
 *
 * @author Carl Alexander
 */
class Helthe_Plugin
{
    /**
     * Current plugin version.
     *
     * @var string
     */
    const VERSION = 'beta';

    /**
     * The basename of the plugin.
     *
     * @var string
     */
    private $basename;

    /**
     * The plugin exception collector.
     *
     * @var Helthe_Exception_Collector
     */
    private $collector;

    /**
     * Flag to track if the plugin is loaded.
     *
     * @var bool
     */
    private $loaded;

    /**
     * The plugin options.
     *
     * @var array
     */
    private $options;

    /**
     * WordPress Plugin API manager.
     *
     * @var Helthe_PluginAPI_Manager
     */
    private $plugin_api_manager;

    /**
     * Constructor.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->basename = plugin_basename($file);
        $this->loaded = false;
        $this->options = get_option('helthe', array());
        $this->plugin_api_manager = new Helthe_PluginAPI_Manager();
        $this->collector = new Helthe_Exception_Collector($this->plugin_api_manager);
    }

    /**
     * Checks if the plugin is loaded.
     *
     * @return bool
     */
    public function is_loaded()
    {
        return $this->loaded;
    }

    /**
     * Loads the plugin into WordPress.
     */
    public function load()
    {
        if ($this->is_loaded()) {
            return;
        }

        // Registering the error handler should always be first
        Helthe_ErrorHandler::register($this->plugin_api_manager, $this->get_error_reporting_level());

        foreach ($this->get_subscribers() as $subscriber) {
            $this->plugin_api_manager->register($subscriber);
        }

        $this->decorate_wpdb();

        $this->loaded = true;
    }

    /**
     * Replaces the global WPDB instance with our decorator.
     */
    private function decorate_wpdb()
    {
        global $wpdb;

        if (!$wpdb instanceof Helthe_Decorator_WPDB) {
            $wpdb = new Helthe_Decorator_WPDB($wpdb, $this->plugin_api_manager);
        }
    }

    /**
     * Get the selected error reporting level bit mask.
     *
     * @return integer|null
     */
    private function get_error_reporting_level()
    {
        if (!isset($this->options['error_reporting'])) {
            return null;
        }

        switch ($this->options['error_reporting']) {
            case 'prod':
                return E_ALL & ~E_STRICT;
            case 'all':
                return E_ALL | E_STRICT;
            case 'none':
                return 0;
            default:
                return null;
        }
    }

    /**
     * Get the plugin options.
     *
     * @return array
     */
    private function get_options()
    {
        return $this->options;
    }

    /**
     * Get the WordPress plugin API subscribers.
     *
     * @return Helthe_PluginAPI_HookSubscriberInterface[]
     */
    private function get_subscribers()
    {
        return array(
            new Helthe_Subscriber_AdminBarSubscriber($this->collector),
            new Helthe_Subscriber_AdminPageSubscriber(new Helthe_Admin_Page($this->get_options())),
            new Helthe_Subscriber_CollectorSubscriber($this->collector),
            new Helthe_Subscriber_DeprecatedSubscriber($this->plugin_api_manager),
            new Helthe_Subscriber_EnsureLoadedFirstSubscriber($this->basename),
            new Helthe_Subscriber_NoticeSubscriber($this->plugin_api_manager),
            new Helthe_Subscriber_SlowHookSubscriber($this->plugin_api_manager),
            new Helthe_Subscriber_WarningSubscriber($this->plugin_api_manager),
            new Helthe_Subscriber_WPDieSubscriber($this->plugin_api_manager),
            new Helthe_Subscriber_WPErrorSubscriber($this->plugin_api_manager),
        );
    }
}
