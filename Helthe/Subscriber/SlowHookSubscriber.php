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
 * Subscriber that looks for slow WordPress hooks. It does so by attaching a verification
 * callback for each hook called while WordPress is running.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Subscriber_SlowHookSubscriber implements Helthe_PluginAPI_HookSubscriberInterface
{
    /**
     * WordPress Plugin API manager.
     *
     * @var Helthe_PluginAPI_Manager
     */
    private $plugin_api_manager;

    /**
     * Array of all the WordPress hooks.
     *
     * @var array
     */
    private $hooks;

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
            'all' => 'start_timer'
        );
    }

    /**
     * Starts a new timer and adds the hook to stop the timer(s) if the hook isn't present.
     */
    public function start_timer()
    {
        $hook_name = $this->plugin_api_manager->get_current_hook();

        // Don't time the hook we use for errors.
        if ($hook_name === 'helthe_slow_hook') {
            return;
        }

        if (!isset($this->hooks[$hook_name])) {
            $this->plugin_api_manager->add_hook($hook_name, array($this, 'stop_timer'), 9999);

            $this->hooks[$hook_name] = array(
              'timers' => array()
            );
        }

        //$this->hooks[$hook_name]['timers'][] = new Helthe_Timer();
    }

    /**
     * Stops the timer and checks if it was a slow hook.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function stop_timer($value = null)
    {
        $hook_name = $this->plugin_api_manager->get_current_hook();
        $timer = array_pop($this->hooks[$hook_name]['timers']);

        if (!$timer instanceof Helthe_Timer) {
            return $value;
        }

        if ($timer->get_time() > 0.2) {
            $this->plugin_api_manager->do_hook('helthe_slow_hook', $hook_name, $timer->get_time());
        }

        return $value;
    }
}