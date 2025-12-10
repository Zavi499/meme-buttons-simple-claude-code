<?php
/**
 * Handle sound file uploads
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SBP_Upload_Handler {

    /**
     * Handle single file upload
     *
     * @param array $file File data from $_FILES
     * @param array $sound_data Sound metadata (title, description, categories)
     * @return int|WP_Error Sound post ID or error
     */
    public static function handle_single_upload($file, $sound_data) {
        // Validate file type
        $file_type = wp_check_filetype($file['name']);

        if ($file_type['ext'] !== 'mp3') {
            return new WP_Error('invalid_file_type', __('Only MP3 files are allowed.', 'sound-buttons'));
        }

        // Handle file upload
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($file, $upload_overrides);

        if (isset($movefile['error'])) {
            return new WP_Error('upload_error', $movefile['error']);
        }

        // Create attachment
        $attachment = array(
            'guid' => $movefile['url'],
            'post_mime_type' => $movefile['type'],
            'post_title' => sanitize_text_field($sound_data['title']),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $movefile['file']);

        if (is_wp_error($attach_id)) {
            return $attach_id;
        }

        // Generate attachment metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $movefile['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);

        // Create sound post
        $post_data = array(
            'post_title' => sanitize_text_field($sound_data['title']),
            'post_content' => wp_kses_post($sound_data['description']),
            'post_status' => 'publish',
            'post_type' => 'sound'
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            // Delete attachment if post creation fails
            wp_delete_attachment($attach_id, true);
            return $post_id;
        }

        // Attach audio file to post
        update_post_meta($post_id, '_sbp_audio_file', $attach_id);
        update_post_meta($post_id, '_sbp_audio_url', $movefile['url']);

        // Assign categories
        if (!empty($sound_data['categories'])) {
            $categories = array_map('intval', (array)$sound_data['categories']);
            wp_set_object_terms($post_id, $categories, 'sound_category');
        }

        // Initialize statistics
        update_post_meta($post_id, '_sbp_play_count', 0);
        update_post_meta($post_id, '_sbp_favorite_count', 0);

        // Generate a random button variant (1-10)
        update_post_meta($post_id, '_sbp_button_variant', rand(1, 10));

        return $post_id;
    }

    /**
     * Handle bulk file upload
     *
     * @param array $files Files array from $_FILES
     * @param array $default_data Default metadata (description, categories)
     * @return array Array of results with success/error for each file
     */
    public static function handle_bulk_upload($files, $default_data) {
        $results = array();

        // Reorganize files array
        $file_count = count($files['name']);

        for ($i = 0; $i < $file_count; $i++) {
            // Skip if no file
            if (empty($files['name'][$i])) {
                continue;
            }

            $file = array(
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            );

            // Generate title from filename
            $title = sanitize_text_field(pathinfo($file['name'], PATHINFO_FILENAME));
            $title = str_replace(array('-', '_'), ' ', $title);
            $title = ucwords($title);

            $sound_data = array(
                'title' => $title,
                'description' => $default_data['description'],
                'categories' => $default_data['categories']
            );

            $result = self::handle_single_upload($file, $sound_data);

            $results[] = array(
                'filename' => $file['name'],
                'success' => !is_wp_error($result),
                'post_id' => is_wp_error($result) ? 0 : $result,
                'error' => is_wp_error($result) ? $result->get_error_message() : ''
            );
        }

        return $results;
    }

    /**
     * Get sound audio URL
     *
     * @param int $post_id Sound post ID
     * @return string|false Audio URL or false
     */
    public static function get_sound_url($post_id) {
        $audio_file_id = get_post_meta($post_id, '_sbp_audio_file', true);

        if ($audio_file_id) {
            return wp_get_attachment_url($audio_file_id);
        }

        return get_post_meta($post_id, '_sbp_audio_url', true);
    }

    /**
     * Get sound file path
     *
     * @param int $post_id Sound post ID
     * @return string|false File path or false
     */
    public static function get_sound_file_path($post_id) {
        $audio_file_id = get_post_meta($post_id, '_sbp_audio_file', true);

        if ($audio_file_id) {
            return get_attached_file($audio_file_id);
        }

        return false;
    }
}
