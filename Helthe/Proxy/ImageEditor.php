<?php

/*
 * This file is part of the WordPress Helthe Monitor plugin.
 *
 * (c) Carl Alexander <carlalexander@helthe.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once ABSPATH . WPINC . '/class-wp-image-editor.php';

/**
 * This is a proxy class around the image editor implementations of WordPress.
 * It checks for errors on important operations.
 *
 * @author Carl Alexander
 */
class Helthe_Proxy_ImageEditor extends WP_Image_Editor
{

    /**
     * All the registered implementations.
     *
     * @var array
     */
    private static $implementations = array();

    /**
     * The implementation used by the proxy.
     *
     * @var string
     */
    private static $chosen_implementation;

    /**
     * @var WP_Image_Editor
     */
    private $editor;

    /**
     * Sets the implementations that the proxy can use.
     *
     * @param array $implementations
     */
    public static function set_implementations(array $implementations)
    {
        self::$implementations = $implementations;
    }

    /**
     * Runs tests on every registered implementation. Saves the chosen implementation.
     * This function mirrors the testing done by _wp_image_editor_choose.
     *
     * @see _wp_image_editor_choose()
     *
     * @param array $args
     *
     * @return bool
     */
    public static function test($args = array())
    {
        foreach (self::$implementations as $implementation) {
            if (!call_user_func(array($implementation, 'test'), $args)) {
                continue;
            }

            if (isset($args['mime_type']) && !call_user_func(array($implementation, 'supports_mime_type'), $args['mime_type'])) {
                continue;
            }

            if (isset($args['methods']) && array_diff($args['methods'], get_class_methods($implementation))) {
                continue;
            }

            self::$chosen_implementation = $implementation;

            return true;
        }

        do_action('helthe_image_editor_not_found', self::$implementations);

        return false;
    }

    /**
     * Checks to see if our chosen implementation supports the mime-type specified.
     *
     * @param string $mime
     *
     * @return boon
     */
    public static function supports_mime_type($mime)
    {
        if (!self::$chosen_implementation) {
            return false;
        }

        return call_user_func(array(self::$chosen_implementation, 'supports_mime_type'), $mime);
    }

    /**
     * Constructor.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->editor = new self::$chosen_implementation($file);
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        return $this->editor->load();
    }

    /**
     * {@inheritdoc}
     */
    public function save($filename = null, $mime = null)
    {
        return $this->editor->save($filename, $mime);
    }

    /**
     * {@inheritdoc}
     */
    public function stream($mime = null)
    {
        return $this->editor->stream($mime);
    }

    /**
     * {@inheritdoc}
     */
    public function resize($maxWidth, $maxHeight, $crop = false)
    {
        return $this->editor->resize($maxWidth, $maxHeight, $crop);
    }

    /**
     * {@inheritdoc}
     */
    public function multi_resize($sizes)
    {
        return $this->editor->multi_resize($sizes);
    }

    /**
     * {@inheritdoc}
     */
    public function crop($srcX, $srcY, $srcWidth, $srcHeight, $dstWidth = null, $dstHeigth = null, $srcAbs = false)
    {
        return $this->editor->crop($srcX, $srcY, $srcWidth, $srcHeight, $dstWidth, $dstHeigth, $srcAbs);
    }

    /**
     * {@inheritdoc}
     */
    public function rotate($angle)
    {
        return $this->editor->rotate($angle);
    }

    /**
     * {@inheritdoc}
     */
    public function flip($horizontal, $vertical)
    {
        return $this->editor->flip($horizontal, $vertical);
    }

    /**
     * {@inheritdoc}
     */
    public function get_size()
    {
        return $this->editor->get_size();
    }

    /**
     * {@inheritdoc}
     */
    public function set_quality($quality)
    {
        return $this->editor->set_quality($quality);
    }

    /**
     * {@inheritdoc}
     */
    public function generate_filename($suffix = null, $path = null, $extension = null)
    {
        return $this->editor->generate_filename($suffix, $path, $extension);
    }

    /**
     * {@inheritdoc}
     */
    public function get_suffix()
    {
        return $this->editor->get_suffix();
    }
}
