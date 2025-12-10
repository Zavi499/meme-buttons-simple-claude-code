<?php
/**
 * Handle sound statistics tracking
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SBP_Statistics {

    /**
     * Track a sound play
     *
     * @param int $sound_id Sound post ID
     * @return bool Success
     */
    public static function track_play($sound_id) {
        global $wpdb;

        $sound_id = absint($sound_id);

        if (!$sound_id || get_post_type($sound_id) !== 'sound') {
            return false;
        }

        // Get user IP
        $user_ip = self::get_user_ip();

        // Insert play record
        $table_name = $wpdb->prefix . 'sbp_statistics';

        $wpdb->insert(
            $table_name,
            array(
                'sound_id' => $sound_id,
                'user_ip' => $user_ip,
                'played_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s')
        );

        // Update play count meta
        $current_count = get_post_meta($sound_id, '_sbp_play_count', true);
        $new_count = absint($current_count) + 1;
        update_post_meta($sound_id, '_sbp_play_count', $new_count);

        return true;
    }

    /**
     * Get play count for a sound
     *
     * @param int $sound_id Sound post ID
     * @return int Play count
     */
    public static function get_play_count($sound_id) {
        $count = get_post_meta($sound_id, '_sbp_play_count', true);
        return absint($count);
    }

    /**
     * Get favorite count for a sound
     *
     * @param int $sound_id Sound post ID
     * @return int Favorite count
     */
    public static function get_favorite_count($sound_id) {
        $count = get_post_meta($sound_id, '_sbp_favorite_count', true);
        return absint($count);
    }

    /**
     * Get trending sounds (most played)
     *
     * @param int $limit Number of sounds to return
     * @param int $days Number of days to look back (0 = all time)
     * @return array Array of sound post IDs
     */
    public static function get_trending_sounds($limit = 10, $days = 7) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'sbp_statistics';

        if ($days > 0) {
            $date_query = $wpdb->prepare(
                "AND played_at >= DATE_SUB(NOW(), INTERVAL %d DAY)",
                $days
            );
        } else {
            $date_query = '';
        }

        $query = "
            SELECT sound_id, COUNT(*) as play_count
            FROM $table_name
            WHERE 1=1 $date_query
            GROUP BY sound_id
            ORDER BY play_count DESC
            LIMIT %d
        ";

        $results = $wpdb->get_results($wpdb->prepare($query, $limit));

        return wp_list_pluck($results, 'sound_id');
    }

    /**
     * AJAX handler for tracking plays
     */
    public static function ajax_track_play() {
        check_ajax_referer('sbp_nonce', 'nonce');

        $sound_id = isset($_POST['sound_id']) ? absint($_POST['sound_id']) : 0;

        if (!$sound_id) {
            wp_send_json_error(array('message' => __('Invalid sound ID', 'sound-buttons')));
        }

        $success = self::track_play($sound_id);

        if ($success) {
            wp_send_json_success(array(
                'play_count' => self::get_play_count($sound_id)
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to track play', 'sound-buttons')));
        }
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
