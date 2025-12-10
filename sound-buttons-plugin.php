<?php
/**
 * Plugin Name: Sound Buttons Plugin
 * Plugin URI: https://soundbuttonsworld.com
 * Description: A comprehensive sound board plugin for WordPress with MP3 sound management, categories, favorites, and sharing features.
 * Version: 1.0.0
 * Author: Sound Buttons Team
 * Author URI: https://soundbuttonsworld.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sound-buttons
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SBP_VERSION', '1.0.0');
define('SBP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SBP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SBP_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Sound Buttons Plugin Class
 */
class Sound_Buttons_Plugin {

    /**
     * Single instance of the class
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Core functionality
        require_once SBP_PLUGIN_DIR . 'includes/class-sbp-post-types.php';
        require_once SBP_PLUGIN_DIR . 'includes/class-sbp-taxonomies.php';
        require_once SBP_PLUGIN_DIR . 'includes/class-sbp-upload-handler.php';
        require_once SBP_PLUGIN_DIR . 'includes/class-sbp-statistics.php';
        require_once SBP_PLUGIN_DIR . 'includes/class-sbp-favorites.php';
        require_once SBP_PLUGIN_DIR . 'includes/class-sbp-download-handler.php';
        require_once SBP_PLUGIN_DIR . 'includes/class-sbp-template-loader.php';

        // Admin functionality
        if (is_admin()) {
            require_once SBP_PLUGIN_DIR . 'admin/class-sbp-admin.php';
            require_once SBP_PLUGIN_DIR . 'admin/class-sbp-admin-upload.php';
            require_once SBP_PLUGIN_DIR . 'admin/class-sbp-admin-categories.php';
        }

        // Public functionality
        require_once SBP_PLUGIN_DIR . 'public/class-sbp-public.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Init hook
        add_action('init', array($this, 'init'));

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // AJAX actions
        add_action('wp_ajax_sbp_toggle_favorite', array('SBP_Favorites', 'ajax_toggle_favorite'));
        add_action('wp_ajax_nopriv_sbp_toggle_favorite', array('SBP_Favorites', 'ajax_toggle_favorite'));
        add_action('wp_ajax_sbp_track_play', array('SBP_Statistics', 'ajax_track_play'));
        add_action('wp_ajax_nopriv_sbp_track_play', array('SBP_Statistics', 'ajax_track_play'));

        // Template hooks
        add_filter('template_include', array('SBP_Template_Loader', 'load_template'));
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize post types
        SBP_Post_Types::register();

        // Initialize taxonomies
        SBP_Taxonomies::register();

        // Load text domain
        load_plugin_textdomain('sound-buttons', false, dirname(SBP_PLUGIN_BASENAME) . '/languages');
    }

    /**
     * Enqueue public assets
     */
    public function enqueue_public_assets() {
        // Styles
        wp_enqueue_style(
            'sbp-public-styles',
            SBP_PLUGIN_URL . 'public/css/style.css',
            array(),
            SBP_VERSION
        );

        // Scripts
        wp_enqueue_script(
            'sbp-public-scripts',
            SBP_PLUGIN_URL . 'public/js/script.js',
            array('jquery'),
            SBP_VERSION,
            true
        );

        // Localize script
        wp_localize_script('sbp-public-scripts', 'sbpData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sbp_nonce'),
            'isLoggedIn' => is_user_logged_in()
        ));
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our admin pages
        if (strpos($hook, 'sound-buttons') === false &&
            get_post_type() !== 'sound') {
            return;
        }

        wp_enqueue_style(
            'sbp-admin-styles',
            SBP_PLUGIN_URL . 'admin/css/admin-style.css',
            array(),
            SBP_VERSION
        );

        wp_enqueue_script(
            'sbp-admin-scripts',
            SBP_PLUGIN_URL . 'admin/js/admin-script.js',
            array('jquery'),
            SBP_VERSION,
            true
        );

        wp_localize_script('sbp-admin-scripts', 'sbpAdminData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sbp_admin_nonce')
        ));
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Register post types and taxonomies
        SBP_Post_Types::register();
        SBP_Taxonomies::register();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Create necessary database tables
        $this->create_tables();

        // Set default options
        $this->set_default_options();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create custom database tables
     */
    private function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Table for tracking sound plays
        $table_name = $wpdb->prefix . 'sbp_statistics';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            sound_id bigint(20) NOT NULL,
            user_ip varchar(100) NOT NULL,
            played_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY sound_id (sound_id),
            KEY played_at (played_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Table for favorites
        $table_name = $wpdb->prefix . 'sbp_favorites';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            sound_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            user_ip varchar(100) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY sound_user (sound_id, user_id, user_ip),
            KEY sound_id (sound_id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    /**
     * Set default plugin options
     */
    private function set_default_options() {
        $defaults = array(
            'sbp_sounds_per_page' => 30,
            'sbp_enable_download_protection' => true,
            'sbp_rate_limit_downloads' => 10,
            'sbp_button_variants' => 10,
            'sbp_enable_statistics' => true,
            'sbp_enable_favorites' => true
        );

        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
}

/**
 * Initialize the plugin
 */
function sound_buttons_plugin() {
    return Sound_Buttons_Plugin::get_instance();
}

// Start the plugin
sound_buttons_plugin();
