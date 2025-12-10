<?php
/**
 * Load custom templates for sounds
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SBP_Template_Loader {

    /**
     * Load appropriate template
     *
     * @param string $template Template path
     * @return string Modified template path
     */
    public static function load_template($template) {
        // Single sound page
        if (is_singular('sound')) {
            $custom_template = self::locate_template('single-sound.php');
            if ($custom_template) {
                return $custom_template;
            }
        }

        // Sound category archive
        if (is_tax('sound_category')) {
            $custom_template = self::locate_template('archive-sound-category.php');
            if ($custom_template) {
                return $custom_template;
            }
        }

        // Sound post type archive
        if (is_post_type_archive('sound')) {
            $custom_template = self::locate_template('archive-sound.php');
            if ($custom_template) {
                return $custom_template;
            }
        }

        return $template;
    }

    /**
     * Locate template file
     *
     * @param string $template_name Template filename
     * @return string|false Template path or false
     */
    private static function locate_template($template_name) {
        // Check in theme first
        $theme_template = locate_template(array(
            'sound-buttons/' . $template_name,
            $template_name
        ));

        if ($theme_template) {
            return $theme_template;
        }

        // Check in plugin templates folder
        $plugin_template = SBP_PLUGIN_DIR . 'templates/' . $template_name;

        if (file_exists($plugin_template)) {
            return $plugin_template;
        }

        return false;
    }

    /**
     * Get template part
     *
     * @param string $slug Template slug
     * @param string $name Template name (optional)
     */
    public static function get_template_part($slug, $name = '') {
        $template = '';

        // Look for {slug}-{name}.php
        if ($name) {
            $template = self::locate_template("{$slug}-{$name}.php");
        }

        // Look for {slug}.php
        if (!$template) {
            $template = self::locate_template("{$slug}.php");
        }

        // Load template
        if ($template) {
            load_template($template, false);
        }
    }
}
