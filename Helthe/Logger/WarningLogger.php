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
 * Helthe warning logger. Trigger E_USER_WARNING errors.
 *
 * @author Carl Alexander
 */
class Helthe_Logger_WarningLogger
{
    /**
     * Register the logger with the appropriate WordPress hooks.
     */
    public static function register()
    {
        $logger = new self();

        add_action('http_api_debug', array($logger, 'logHttp'), 10, 5);
        add_action('helthe_image_editor_not_found', array($logger, 'triggerError'));
        add_action('helthe_filesystem_not_found', array($logger, 'triggerError'));
        add_action('helthe_wpdb_database_error', array($logger, 'triggerError'));
        add_action('helthe_wp_die_ajax_error', array($logger, 'triggerError'));
        add_action('helthe_wp_die_default_error', array($logger, 'triggerError'));
        add_action('helthe_wp_die_xml_error', array($logger, 'triggerError'));
    }

    /**
     * Checks http responses for errors.
     *
     * @param mixed  $response
     * @param string $type
     * @param string $class
     * @param array  $args
     * @param string $url
     */
    public function logHttp($response, $type, $class, $args, $url)
    {
        if (!$response instanceof WP_Error) {
            return;
        }

        $this->triggerError(sprintf('%1$s returned "%2$s" when trying to reach "%3$s".', $class, $response->errors['http_request_failed'][0], $url));
    }

    /**
     * Trigger a PHP error.
     *
     * @param string $message
     */
    public function triggerError($message)
    {
        do_action('helthe_trigger_warning', $message);

        trigger_error($message, E_USER_WARNING);
    }
}
