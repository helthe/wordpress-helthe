<?php

/*
 * This file is part of the WordPress Helthe Monitor plugin.
 *
 * (c) Carl Alexander <carlalexander@helthe.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Subscriber that generates the WordPress admin bar.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Subscriber_AdminBarSubscriber implements Helthe_PluginAPI_HookSubscriberInterface
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
            'admin_bar_menu' => 'generate'
        );
    }

    /**
     * Generate the admin bar menu.
     *
     * @param WP_Admin_Bar $bar
     */
    public function generate(WP_Admin_Bar $bar)
    {
        if (!is_super_admin() || !is_admin_bar_showing()) {
            return;
        }

        $exceptions = $this->collector->get_exceptions();

        $count = count($exceptions);
        $title = sprintf(_n('1 error detected during this page load', '%s errors detected during this page load', $count, 'helthe'), $count);

        $bar->add_node(array(
            'id'     => 'helthe',
            'title'  => sprintf(_n('1 Error', '%s Errors', $count, 'helthe'), $count),
            'meta'   => array('title' => $title),
            'parent' => 'top-secondary'
        ));

        foreach ($exceptions as $i => $exception) {
            $bar->add_node(array(
                'id'     => 'helthe_' . $i,
                'title'  => $exception->getMessage(),
                'parent' => 'helthe'
            ));
        }
    }

    /**
     * Builds the admin bar node title from the given exception.
     *
     * @param Exception $exception
     *
     * @return string
     */
    private function build_title(Exception $exception)
    {
        $level = E_RECOVERABLE_ERROR;
        $levels = array(
            E_WARNING           => __('Warning', 'helthe'),
            E_NOTICE            => __('Notice', 'helthe'),
            E_USER_ERROR        => __('User Error', 'helthe'),
            E_USER_WARNING      => __('User Warning', 'helthe'),
            E_USER_NOTICE       => __('User Notice', 'helthe'),
            E_STRICT            => __('Runtime Notice', 'helthe'),
            E_RECOVERABLE_ERROR => __('Catchable Fatal Error', 'helthe'),
            E_DEPRECATED        => __('Deprecated', 'helthe'),
            E_USER_DEPRECATED   => __('User Deprecated', 'helthe'),
            E_ERROR             => __('Error', 'helthe'),
            E_CORE_ERROR        => __('Core Error', 'helthe'),
            E_COMPILE_ERROR     => __('Compile Error', 'helthe'),
            E_PARSE             => __('Parse', 'helthe'),
        );

        if ($exception instanceof ErrorException) {
            $level = $exception->getSeverity();
        }

        return sprintf('%s: %s in %s line %d', isset($levels[$level]) ? $levels[$level] : $level, $exception->getMessage(), $exception->getFile(), $exception->getLine());
    }
}