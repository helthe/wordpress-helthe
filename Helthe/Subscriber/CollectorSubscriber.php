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
 * Subscriber that collects exceptions sent to the plugin API.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Subscriber_CollectorSubscriber implements Helthe_PluginAPI_HookSubscriberInterface
{
    /**
     * The plugin exception collector.
     *
     * @var Helthe_Exception_Collector
     */
    private $collector;

    /**
     * Constructor.
     *
     * @param Helthe_Exception_Collector $collector
     */
    public function __construct(Helthe_Exception_Collector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * {@inheritdoc}
     */
    public static function get_hooks()
    {
        return array(
            'helthe_handle_exception' => 'collect_exception'
        );
    }

    /**
     * Collects the exception sent to the plugin API.
     *
     * @param Exception $exception
     */
    public function collect_exception(Exception $exception)
    {
        $this->collector->collect_exception($exception);
    }
}