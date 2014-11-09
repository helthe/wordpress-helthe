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
 * Handles the rendering of the plugin admin page.
 *
 * @author Carl Alexander
 */
class Helthe_Admin_Page
{
    /**
     * Slug used by the admin page.
     *
     * @var string
     */
    const SLUG = 'helthe';

    /**
     * The plugin options.
     *
     * @var array
     */
    private $options;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * Renders the admin page using the Settings API.
     */
    public function render()
    {
        ?>
        <div class="wrap" id="helthe-admin">
            <div id="icon-tools" class="icon32"><br></div>
            <h2><?php _e('Helthe Monitor Configuration', 'helthe'); ?></h2>
            <form action="options.php" method="POST">
                <?php settings_fields('helthe'); ?>
                <?php do_settings_sections('helthe'); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Renders the general section.
     */
    public function renderGeneralSection()
    {
        ?>
        <p><?php _e('These settings help WordPress log errors.', 'helthe'); ?></p>
        <?php
    }

    /**
     * Renders the error reporting field.
     */
    public function renderErrorReportingField()
    {
        $option = isset($this->options['error_reporting']) ? $this->options['error_reporting'] : null;
        ?>
        <select id="helthe_error_reporting" name="helthe[error_reporting]">
            <option><?php _e('WordPress Default', 'helthe'); ?></option>
            <option value="prod" <?php selected($option, 'prod'); ?>><?php _e('Production Server', 'helthe'); ?></option>
            <option value="all" <?php selected($option, 'all'); ?>><?php _e('All Errors', 'helthe'); ?></option>
            <option value="none" <?php selected($option, 'none'); ?>><?php _e('None', 'helthe'); ?></option>
        </select>
        <p class="description"><?php _e('This allows you to configure what errors get logged by PHP.', 'helthe'); ?></p>
        <?php
    }
}
