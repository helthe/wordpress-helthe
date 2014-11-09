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
 * Subscriber that registers the image editor proxy class with WordPress.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Subscriber_ImageEditorSubscriber implements Helthe_PluginAPI_HookSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function get_hooks()
    {
        return array(
            'wp_image_editors' => array('register_proxy', 9999)
        );
    }

    /**
     * Registers our proxy implementation with WordPress. Saves all the implementations in the proxy class.
     *
     * @param array $implementations
     *
     * @return array
     */
    public function register_proxy(array $implementations)
    {
        Helthe_Proxy_ImageEditor::set_implementations($implementations);

        return array('Helthe_Proxy_ImageEditor');
    }
}