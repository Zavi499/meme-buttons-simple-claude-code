<?php
/**
 * Admin functionality
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SBP_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_sound', array($this, 'save_sound_meta'), 10, 2);
    }

    /**
     * Add admin menu pages
     */
    public function add_admin_menu() {
        // Main menu is created by the post type
        // Add submenu pages

        add_submenu_page(
            'edit.php?post_type=sound',
            __('Upload Sounds', 'sound-buttons'),
            __('Upload Sounds', 'sound-buttons'),
            'upload_files',
            'sbp-upload',
            array('SBP_Admin_Upload', 'render_upload_page')
        );

        add_submenu_page(
            'edit.php?post_type=sound',
            __('Settings', 'sound-buttons'),
            __('Settings', 'sound-buttons'),
            'manage_options',
            'sbp-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        // Sound file meta box
        add_meta_box(
            'sbp_sound_file',
            __('Sound File', 'sound-buttons'),
            array($this, 'render_sound_file_meta_box'),
            'sound',
            'side',
            'high'
        );

        // Statistics meta box
        add_meta_box(
            'sbp_statistics',
            __('Statistics', 'sound-buttons'),
            array($this, 'render_statistics_meta_box'),
            'sound',
            'side',
            'default'
        );

        // Button variant meta box
        add_meta_box(
            'sbp_button_variant',
            __('Button Style', 'sound-buttons'),
            array($this, 'render_button_variant_meta_box'),
            'sound',
            'side',
            'default'
        );
    }

    /**
     * Render sound file meta box
     */
    public function render_sound_file_meta_box($post) {
        $audio_file_id = get_post_meta($post->ID, '_sbp_audio_file', true);
        $audio_url = SBP_Upload_Handler::get_sound_url($post->ID);

        wp_nonce_field('sbp_sound_meta', 'sbp_sound_meta_nonce');
        ?>
        <div class="sbp-sound-file-meta">
            <?php if ($audio_url): ?>
                <div class="sbp-audio-player">
                    <audio controls style="width: 100%; margin-bottom: 10px;">
                        <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
                    </audio>
                    <p>
                        <a href="<?php echo esc_url($audio_url); ?>" target="_blank" class="button">
                            <?php _e('View File', 'sound-buttons'); ?>
                        </a>
                    </p>
                </div>
            <?php else: ?>
                <p><?php _e('No sound file attached.', 'sound-buttons'); ?></p>
            <?php endif; ?>

            <p>
                <label>
                    <strong><?php _e('Audio File ID:', 'sound-buttons'); ?></strong><br>
                    <input type="number" name="sbp_audio_file" value="<?php echo esc_attr($audio_file_id); ?>" class="widefat">
                </label>
            </p>
            <p class="description">
                <?php _e('Enter the media library attachment ID for the MP3 file.', 'sound-buttons'); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Render statistics meta box
     */
    public function render_statistics_meta_box($post) {
        $play_count = SBP_Statistics::get_play_count($post->ID);
        $favorite_count = SBP_Statistics::get_favorite_count($post->ID);
        $download_count = get_post_meta($post->ID, '_sbp_download_count', true);
        ?>
        <div class="sbp-statistics-meta">
            <p>
                <strong><?php _e('Plays:', 'sound-buttons'); ?></strong>
                <?php echo number_format_i18n($play_count); ?>
            </p>
            <p>
                <strong><?php _e('Favorites:', 'sound-buttons'); ?></strong>
                <?php echo number_format_i18n($favorite_count); ?>
            </p>
            <p>
                <strong><?php _e('Downloads:', 'sound-buttons'); ?></strong>
                <?php echo number_format_i18n($download_count); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Render button variant meta box
     */
    public function render_button_variant_meta_box($post) {
        $variant = get_post_meta($post->ID, '_sbp_button_variant', true);
        if (!$variant) {
            $variant = 1;
        }
        ?>
        <p>
            <label>
                <strong><?php _e('Button Variant:', 'sound-buttons'); ?></strong><br>
                <select name="sbp_button_variant" class="widefat">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php selected($variant, $i); ?>>
                            <?php printf(__('Variant %d', 'sound-buttons'), $i); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </label>
        </p>
        <p class="description">
            <?php _e('Select the visual style for this sound button.', 'sound-buttons'); ?>
        </p>
        <?php
    }

    /**
     * Save sound meta
     */
    public function save_sound_meta($post_id, $post) {
        // Verify nonce
        if (!isset($_POST['sbp_sound_meta_nonce']) || !wp_verify_nonce($_POST['sbp_sound_meta_nonce'], 'sbp_sound_meta')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save audio file ID
        if (isset($_POST['sbp_audio_file'])) {
            $audio_file_id = absint($_POST['sbp_audio_file']);
            update_post_meta($post_id, '_sbp_audio_file', $audio_file_id);

            // Update audio URL
            if ($audio_file_id) {
                $audio_url = wp_get_attachment_url($audio_file_id);
                update_post_meta($post_id, '_sbp_audio_url', $audio_url);
            }
        }

        // Save button variant
        if (isset($_POST['sbp_button_variant'])) {
            $variant = absint($_POST['sbp_button_variant']);
            if ($variant >= 1 && $variant <= 10) {
                update_post_meta($post_id, '_sbp_button_variant', $variant);
            }
        }
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        // Handle form submission
        if (isset($_POST['sbp_save_settings'])) {
            check_admin_referer('sbp_settings_nonce');

            $settings = array(
                'sbp_sounds_per_page' => absint($_POST['sounds_per_page']),
                'sbp_enable_download_protection' => isset($_POST['enable_download_protection']),
                'sbp_rate_limit_downloads' => absint($_POST['rate_limit_downloads']),
                'sbp_enable_statistics' => isset($_POST['enable_statistics']),
                'sbp_enable_favorites' => isset($_POST['enable_favorites'])
            );

            foreach ($settings as $key => $value) {
                update_option($key, $value);
            }

            echo '<div class="notice notice-success"><p>' . __('Settings saved.', 'sound-buttons') . '</p></div>';
        }

        // Get current settings
        $sounds_per_page = get_option('sbp_sounds_per_page', 30);
        $enable_download_protection = get_option('sbp_enable_download_protection', true);
        $rate_limit = get_option('sbp_rate_limit_downloads', 10);
        $enable_statistics = get_option('sbp_enable_statistics', true);
        $enable_favorites = get_option('sbp_enable_favorites', true);
        ?>
        <div class="wrap">
            <h1><?php _e('Sound Buttons Settings', 'sound-buttons'); ?></h1>

            <form method="post" action="">
                <?php wp_nonce_field('sbp_settings_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="sounds_per_page"><?php _e('Sounds Per Page', 'sound-buttons'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="sounds_per_page" name="sounds_per_page"
                                   value="<?php echo esc_attr($sounds_per_page); ?>" min="1" max="100" class="small-text">
                            <p class="description">
                                <?php _e('Number of sounds to display on archive pages.', 'sound-buttons'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Download Protection', 'sound-buttons'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_download_protection" value="1"
                                    <?php checked($enable_download_protection); ?>>
                                <?php _e('Enable download protection', 'sound-buttons'); ?>
                            </label>
                            <p class="description">
                                <?php _e('Protect downloads with nonce verification and rate limiting.', 'sound-buttons'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="rate_limit_downloads"><?php _e('Download Rate Limit', 'sound-buttons'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="rate_limit_downloads" name="rate_limit_downloads"
                                   value="<?php echo esc_attr($rate_limit); ?>" min="1" max="100" class="small-text">
                            <p class="description">
                                <?php _e('Maximum downloads per IP address per hour.', 'sound-buttons'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Features', 'sound-buttons'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_statistics" value="1"
                                    <?php checked($enable_statistics); ?>>
                                <?php _e('Enable play statistics tracking', 'sound-buttons'); ?>
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" name="enable_favorites" value="1"
                                    <?php checked($enable_favorites); ?>>
                                <?php _e('Enable favorites system', 'sound-buttons'); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" name="sbp_save_settings" class="button button-primary">
                        <?php _e('Save Settings', 'sound-buttons'); ?>
                    </button>
                </p>
            </form>
        </div>
        <?php
    }
}

// Initialize admin
new SBP_Admin();
