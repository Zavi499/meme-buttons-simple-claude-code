<?php
/**
 * Handle favorite sounds functionality
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SBP_Favorites {

    /**
     * Toggle favorite status for a sound
     *
     * @param int $sound_id Sound post ID
     * @return bool|string 'added', 'removed', or false on error
     */
    public static function toggle_favorite($sound_id) {
        global $wpdb;

        $sound_id = absint($sound_id);

        if (!$sound_id || get_post_type($sound_id) !== 'sound') {
            return false;
        }

        $table_name = $wpdb->prefix . 'sbp_favorites';
        $user_id = get_current_user_id();
        $user_ip = self::get_user_ip();

        // Check if already favorited
        if ($user_id > 0) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE sound_id = %d AND user_id = %d",
                $sound_id,
                $user_id
            ));
        } else {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE sound_id = %d AND user_ip = %s AND user_id IS NULL",
                $sound_id,
                $user_ip
            ));
        }

        if ($exists) {
            // Remove favorite
            $wpdb->delete(
                $table_name,
                array('id' => $exists),
                array('%d')
            );

            // Update count
            $current_count = get_post_meta($sound_id, '_sbp_favorite_count', true);
            $new_count = max(0, absint($current_count) - 1);
            update_post_meta($sound_id, '_sbp_favorite_count', $new_count);

            return 'removed';
        } else {
            // Add favorite
            $wpdb->insert(
                $table_name,
                array(
                    'sound_id' => $sound_id,
                    'user_id' => $user_id > 0 ? $user_id : null,
                    'user_ip' => $user_ip,
                    'created_at' => current_time('mysql')
                ),
                array('%d', '%d', '%s', '%s')
            );

            // Update count
            $current_count = get_post_meta($sound_id, '_sbp_favorite_count', true);
            $new_count = absint($current_count) + 1;
            update_post_meta($sound_id, '_sbp_favorite_count', $new_count);

            return 'added';
        }
    }

    /**
     * Check if a sound is favorited by current user
     *
     * @param int $sound_id Sound post ID
     * @return bool True if favorited
     */
    public static function is_favorited($sound_id) {
        global $wpdb;

        $sound_id = absint($sound_id);
        $table_name = $wpdb->prefix . 'sbp_favorites';
        $user_id = get_current_user_id();
        $user_ip = self::get_user_ip();

        if ($user_id > 0) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE sound_id = %d AND user_id = %d",
                $sound_id,
                $user_id
            ));
        } else {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE sound_id = %d AND user_ip = %s AND user_id IS NULL",
                $sound_id,
                $user_ip
            ));
        }

        return (bool)$exists;
    }

    /**
     * Get user's favorite sounds
     *
     * @param int $user_id User ID (0 for current user based on IP)
     * @return array Array of sound post IDs
     */
    public static function get_user_favorites($user_id = 0) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'sbp_favorites';

        if ($user_id > 0) {
            $results = $wpdb->get_col($wpdb->prepare(
                "SELECT sound_id FROM $table_name WHERE user_id = %d ORDER BY created_at DESC",
                $user_id
            ));
        } else {
            $user_ip = self::get_user_ip();
            $current_user_id = get_current_user_id();

            if ($current_user_id > 0) {
                $results = $wpdb->get_col($wpdb->prepare(
                    "SELECT sound_id FROM $table_name WHERE user_id = %d ORDER BY created_at DESC",
                    $current_user_id
                ));
            } else {
                $results = $wpdb->get_col($wpdb->prepare(
                    "SELECT sound_id FROM $table_name WHERE user_ip = %s AND user_id IS NULL ORDER BY created_at DESC",
                    $user_ip
                ));
            }
        }

        return $results ? array_map('absint', $results) : array();
    }

    /**
     * AJAX handler for toggling favorites
     */
    public static function ajax_toggle_favorite() {
        check_ajax_referer('sbp_nonce', 'nonce');

        $sound_id = isset($_POST['sound_id']) ? absint($_POST['sound_id']) : 0;

        if (!$sound_id) {
            wp_send_json_error(array('message' => __('Invalid sound ID', 'sound-buttons')));
        }

        $result = self::toggle_favorite($sound_id);

        if ($result) {
            wp_send_json_success(array(
                'action' => $result,
                'favorite_count' => SBP_Statistics::get_favorite_count($sound_id),
                'is_favorited' => $result === 'added'
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to update favorite', 'sound-buttons')));
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
