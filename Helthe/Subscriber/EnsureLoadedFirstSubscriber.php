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
     * The basename of the plugin.
     *
     * @var string
     */
    private $basename;

    /**
     * {@inheritdoc}
     */
    public static function get_hooks()
    {
        return array(
            'pre_update_option_active_plugins' => array('ensure_loaded_first', 9999)
        );
    }

    /**
     * Constructor
     *
     * @param string $basename
     */
    public function __construct($basename)
    {
        $this->basename = $basename;
    }

    /**
     * Ensures that the plugin is always the first one to be loaded.
     */
    public function ensure_loaded_first(array $plugins)
    {
        $key = array_search($this->basename, $plugins);

        if (false !== $key) {
            array_splice($plugins, $key, 1);
            array_unshift($plugins, $this->basename);
        }

        return $plugins;
    }
}