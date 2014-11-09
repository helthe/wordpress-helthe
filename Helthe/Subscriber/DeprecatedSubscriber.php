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
 * Subscriber that tracks the use of deprecated WordPress functions.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Subscriber_DeprecatedSubscriber implements Helthe_PluginAPI_HookSubscriberInterface
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
            'deprecated_function_run'  => array('deprecated_function', 10, 3),
            'deprecated_file_included' => array('deprecated_file', 10, 4),
            'deprecated_argument_run'  => array('deprecated_argument', 10, 4),
        );
    }
    
    /**
     * Triggered when a deprecated argument is used.
     *
     * @param string $function
     * @param string $message
     * @param string $version
     */
    public function deprecated_argument($function, $message, $version)
    {
        $error_message = sprintf(__('%1$s was called with an argument that is deprecated since version %2$s with no alternative available.', 'helthe'), $function, $version);

        if (null !== $message) {
            $error_message = sprintf(__('%1$s was called with an argument that is deprecated since version %2$s! %3$s', 'helthe'), $function, $version, $message);
        }

        $this->trigger_deprecated($error_message);
    }
    
    /**
     * Triggered when a deprecated function is included.
     *
     * @param string $file
     * @param string $replacement
     * @param string $version
     * @param string $message
     */
    public function deprecated_file($file, $replacement, $version, $message)
    {
        $error_message = sprintf(__('%1$s is deprecated since version %2$s with no alternative available.', 'helthe'), $file, $version) . $message;

        if (null !== $replacement) {
            $error_message = sprintf(__('%1$s is deprecated since version %2$s! Use %3$s instead.', 'helthe'), $file, $version, $replacement) . $message;
        }

        $this->trigger_deprecated($error_message);
    }

    /**
     * Triggered when a deprecated function is run.
     *
     * @param string $function
     * @param string $replacement
     * @param string $version
     */
    public function deprecated_function($function, $replacement, $version)
    {
        $error_message = sprintf(__('%1$s is deprecated since version %2$s with no alternative available.', 'helthe'), $function, $version);

        if (null !== $replacement) {
            $error_message = sprintf(__('%1$s is deprecated since version %2$s! Use %3$s instead.', 'helthe'), $function, $version, $replacement);
        }

        $this->trigger_deprecated($error_message);
    }

    /**
     * Trigger a deprecated PHP error.
     *
     * @param string $message
     */
    private function trigger_deprecated($message)
    {
        $type = E_USER_NOTICE;

        // Cannot use E_USER_DEPRECATED before PHP 5.3.
        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
            $type = E_USER_DEPRECATED;
        }

        $this->plugin_api_manager->do_hook('helthe_trigger_deprecated_error', $message);

        trigger_error($message, $type);
    }
}