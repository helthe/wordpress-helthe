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
 * Subscriber that monitors for WordPress issues that should trigger a notice in PHP.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Subscriber_NoticeSubscriber implements Helthe_PluginAPI_HookSubscriberInterface
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
            'doing_it_wrong_run' => array('doing_it_wrong', 10, 2)
        );
    }

    /**
     * Triggered when a function is improperly called.
     *
     * @param string $function
     * @param string $message
     */
    public function doing_it_wrong($function, $message)
    {
        $this->trigger_notice(sprintf(__('"%1$s" was called incorrectly. %2$s', 'helthe'), $function, $message));
    }

    /**
     * Trigger a PHP notice.
     *
     * @param string $message
     */
    private function trigger_notice($message)
    {
        $this->plugin_api_manager->do_hook('helthe_trigger_notice_error', $message);

        trigger_error($message, E_USER_NOTICE);
    }
}