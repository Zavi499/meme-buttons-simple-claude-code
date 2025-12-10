<?php
/**
 * Handle secure sound downloads
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SBP_Download_Handler {

    /**
     * Initialize download handler
     */
    public static function init() {
        add_action('template_redirect', array(__CLASS__, 'handle_download_request'));
    }

    /**
     * Handle download requests
     */
    public static function handle_download_request() {
        if (!isset($_GET['sbp_download']) || !isset($_GET['sound_id'])) {
            return;
        }

        $sound_id = absint($_GET['sound_id']);
        $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';

        // Verify nonce
        if (!wp_verify_nonce($nonce, 'sbp_download_' . $sound_id)) {
            wp_die(__('Security check failed. Please try again.', 'sound-buttons'), 'Error', array('response' => 403));
        }

        // Check rate limiting
        if (!self::check_rate_limit()) {
            wp_die(__('Download limit exceeded. Please try again later.', 'sound-buttons'), 'Error', array('response' => 429));
        }

        // Validate sound
        if (get_post_type($sound_id) !== 'sound') {
            wp_die(__('Invalid sound.', 'sound-buttons'), 'Error', array('response' => 404));
        }

        // Get file path
        $file_path = SBP_Upload_Handler::get_sound_file_path($sound_id);

        if (!$file_path || !file_exists($file_path)) {
            wp_die(__('Sound file not found.', 'sound-buttons'), 'Error', array('response' => 404));
        }

        // Validate user agent (prevent bots)
        if (!self::validate_user_agent()) {
            wp_die(__('Invalid request.', 'sound-buttons'), 'Error', array('response' => 403));
        }

        // Log download
        self::log_download($sound_id);

        // Force download
        self::force_download($file_path, get_the_title($sound_id) . '.mp3');
    }

    /**
     * Generate download URL
     *
     * @param int $sound_id Sound post ID
     * @return string Download URL
     */
    public static function get_download_url($sound_id) {
        $nonce = wp_create_nonce('sbp_download_' . $sound_id);

        return add_query_arg(array(
            'sbp_download' => '1',
            'sound_id' => $sound_id,
            '_wpnonce' => $nonce
        ), home_url('/'));
    }

    /**
     * Check rate limiting
     *
     * @return bool True if allowed
     */
    private static function check_rate_limit() {
        $user_ip = self::get_user_ip();
        $transient_key = 'sbp_download_count_' . md5($user_ip);

        $download_count = get_transient($transient_key);
        $rate_limit = get_option('sbp_rate_limit_downloads', 10);

        if ($download_count === false) {
            // First download in this period
            set_transient($transient_key, 1, HOUR_IN_SECONDS);
            return true;
        }

        if ($download_count >= $rate_limit) {
            return false;
        }

        // Increment count
        set_transient($transient_key, $download_count + 1, HOUR_IN_SECONDS);
        return true;
    }

    /**
     * Validate user agent
     *
     * @return bool True if valid
     */
    private static function validate_user_agent() {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        // Block common bot user agents
        $blocked_patterns = array(
            'bot', 'crawl', 'spider', 'scrape', 'curl', 'wget'
        );

        foreach ($blocked_patterns as $pattern) {
            if (stripos($user_agent, $pattern) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Log download
     *
     * @param int $sound_id Sound post ID
     */
    private static function log_download($sound_id) {
        $current_downloads = get_post_meta($sound_id, '_sbp_download_count', true);
        $new_count = absint($current_downloads) + 1;
        update_post_meta($sound_id, '_sbp_download_count', $new_count);
    }

    /**
     * Force file download
     *
     * @param string $file_path Path to file
     * @param string $filename Download filename
     */
    private static function force_download($file_path, $filename) {
        // Clean output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Set headers
        header('Content-Description: File Transfer');
        header('Content-Type: audio/mpeg');
        header('Content-Disposition: attachment; filename="' . sanitize_file_name($filename) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        // Output file
        readfile($file_path);
        exit;
    }

    /**
     * Get user IP address
     *
     * @return string IP address
     */
    private static function get_user_ip() {
        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return sanitize_text_field($ip);
    }
}

// Initialize download handler
SBP_Download_Handler::init();
