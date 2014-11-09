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
 * Subscriber that ensures that the Helthe plugin is always loaded first. This is critical otherwise
 * the plugin might not capture as many errors.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Subscriber_EnsureLoadedFirstSubscriber implements Helthe_PluginAPI_HookSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function get_hooks()
    {
        return array(
            'activated_plugin' => 'ensure_loaded_first'
        );
    }

    /**
     * Ensures that the plugin is always the first one to be loaded.
     */
    public function ensure_loaded_first()
    {
        $plugin = 'helthe-monitor/wp-helthe.php';
        $plugins = get_option('active_plugins');
        $key = array_search($plugin, $plugins);

        if (false !== $key) {
            array_splice($plugins, $key, 1);
            array_unshift($plugins, $plugin);
            update_option('active_plugins', $plugins);
        }
    }
}