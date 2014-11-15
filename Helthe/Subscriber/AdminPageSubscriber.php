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
 * Subscriber that registers the admin page with WordPress.
 *
 * @author Carl Alexander <carlalexander@helthe.co>
 */
class Helthe_Subscriber_AdminPageSubscriber implements Helthe_PluginAPI_HookSubscriberInterface
{
    /**
     * The admin page.
     *
     * @var Helthe_Admin_Page
     */
    private $page;

    /**
     * Constructor.
     *
     * @param Helthe_Admin_Page $page
     */
    public function __construct(Helthe_Admin_Page $page)
    {
        $this->page = $page;
    }

    /**
     * {@inheritdoc}
     */
    public static function get_hooks()
    {
        return array(
            'admin_init' => 'configure',
            'admin_menu' => 'add_admin_page'
        );
    }

    /**
     * Adds the admin page to the options menu.
     */
    public function add_admin_page()
    {
        add_options_page(__('Helthe Monitor', 'helthe'), __('Helthe Monitor', 'helthe'), 'install_plugins', Helthe_Admin_Page::SLUG, array($this->page, 'render'));
    }

    /**
     * Configure the admin page using the Settings API.
     */
    public function configure()
    {
        // Register settings
        register_setting('helthe', 'helthe');

        // General Section
        add_settings_section('helthe-general', __('General', 'helthe'), array($this->page, 'renderGeneralSection'), 'helthe');
        add_settings_field('helthe-error-reporting', __('Error Reporting Level', 'helthe'), array($this->page, 'renderErrorReportingField'), 'helthe', 'helthe-general');
    }
}