<?php
/**
 * Admin upload interface
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SBP_Admin_Upload {

    /**
     * Render upload page
     */
    public static function render_upload_page() {
        // Handle form submission
        $upload_results = null;

        if (isset($_POST['sbp_upload_submit'])) {
            check_admin_referer('sbp_upload_nonce');

            if (isset($_POST['upload_type']) && $_POST['upload_type'] === 'bulk') {
                $upload_results = self::handle_bulk_upload();
            } else {
                $upload_results = self::handle_single_upload();
            }
        }

        // Get categories for dropdown
        $categories = get_terms(array(
            'taxonomy' => 'sound_category',
            'hide_empty' => false
        ));
        ?>
        <div class="wrap">
            <h1><?php _e('Upload Sounds', 'sound-buttons'); ?></h1>

            <?php if ($upload_results): ?>
                <?php if ($upload_results['success']): ?>
                    <div class="notice notice-success">
                        <p><?php echo esc_html($upload_results['message']); ?></p>
                        <?php if (!empty($upload_results['details'])): ?>
                            <ul>
                                <?php foreach ($upload_results['details'] as $detail): ?>
                                    <li><?php echo esc_html($detail); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="notice notice-error">
                        <p><?php echo esc_html($upload_results['message']); ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="sbp-upload-tabs">
                <h2 class="nav-tab-wrapper">
                    <a href="#single-upload" class="nav-tab nav-tab-active"><?php _e('Single Upload', 'sound-buttons'); ?></a>
                    <a href="#bulk-upload" class="nav-tab"><?php _e('Bulk Upload', 'sound-buttons'); ?></a>
                </h2>

                <!-- Single Upload Tab -->
                <div id="single-upload" class="sbp-tab-content">
                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('sbp_upload_nonce'); ?>
                        <input type="hidden" name="upload_type" value="single">

                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="sound_file"><?php _e('Sound File *', 'sound-buttons'); ?></label>
                                </th>
                                <td>
                                    <input type="file" id="sound_file" name="sound_file" accept=".mp3,audio/mpeg" required>
                                    <p class="description"><?php _e('Upload an MP3 file.', 'sound-buttons'); ?></p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="sound_title"><?php _e('Sound Title *', 'sound-buttons'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="sound_title" name="sound_title" class="regular-text" required>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="sound_description"><?php _e('Description', 'sound-buttons'); ?></label>
                                </th>
                                <td>
                                    <textarea id="sound_description" name="sound_description" rows="4" class="large-text"></textarea>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="sound_categories"><?php _e('Categories', 'sound-buttons'); ?></label>
                                </th>
                                <td>
                                    <select id="sound_categories" name="sound_categories[]" multiple class="widefat" style="height: 150px;">
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo esc_attr($category->term_id); ?>">
                                                <?php echo esc_html($category->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description">
                                        <?php _e('Hold Ctrl/Cmd to select multiple categories.', 'sound-buttons'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="submit" name="sbp_upload_submit" class="button button-primary">
                                <?php _e('Upload Sound', 'sound-buttons'); ?>
                            </button>
                        </p>
                    </form>
                </div>

                <!-- Bulk Upload Tab -->
                <div id="bulk-upload" class="sbp-tab-content" style="display: none;">
                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('sbp_upload_nonce'); ?>
                        <input type="hidden" name="upload_type" value="bulk">

                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="bulk_files"><?php _e('Sound Files *', 'sound-buttons'); ?></label>
                                </th>
                                <td>
                                    <input type="file" id="bulk_files" name="bulk_files[]" accept=".mp3,audio/mpeg" multiple required>
                                    <p class="description">
                                        <?php _e('Select multiple MP3 files. File names will be used as titles.', 'sound-buttons'); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="bulk_description"><?php _e('Default Description', 'sound-buttons'); ?></label>
                                </th>
                                <td>
                                    <textarea id="bulk_description" name="bulk_description" rows="4" class="large-text"></textarea>
                                    <p class="description">
                                        <?php _e('This description will be applied to all uploaded sounds.', 'sound-buttons'); ?>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    <label for="bulk_categories"><?php _e('Categories', 'sound-buttons'); ?></label>
                                </th>
                                <td>
                                    <select id="bulk_categories" name="bulk_categories[]" multiple class="widefat" style="height: 150px;">
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo esc_attr($category->term_id); ?>">
                                                <?php echo esc_html($category->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description">
                                        <?php _e('Hold Ctrl/Cmd to select multiple categories.', 'sound-buttons'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="submit" name="sbp_upload_submit" class="button button-primary">
                                <?php _e('Upload Sounds', 'sound-buttons'); ?>
                            </button>
                        </p>
                    </form>
                </div>
            </div>

            <script>
            jQuery(document).ready(function($) {
                $('.nav-tab').on('click', function(e) {
                    e.preventDefault();
                    var target = $(this).attr('href');

                    $('.nav-tab').removeClass('nav-tab-active');
                    $(this).addClass('nav-tab-active');

                    $('.sbp-tab-content').hide();
                    $(target).show();
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * Handle single file upload
     */
    private static function handle_single_upload() {
        if (empty($_FILES['sound_file']) || $_FILES['sound_file']['error'] !== UPLOAD_ERR_OK) {
            return array(
                'success' => false,
                'message' => __('No file uploaded or upload error occurred.', 'sound-buttons')
            );
        }

        $sound_data = array(
            'title' => sanitize_text_field($_POST['sound_title']),
            'description' => wp_kses_post($_POST['sound_description']),
            'categories' => isset($_POST['sound_categories']) ? array_map('absint', $_POST['sound_categories']) : array()
        );

        $result = SBP_Upload_Handler::handle_single_upload($_FILES['sound_file'], $sound_data);

        if (is_wp_error($result)) {
            return array(
                'success' => false,
                'message' => $result->get_error_message()
            );
        }

        return array(
            'success' => true,
            'message' => sprintf(
                __('Sound uploaded successfully! <a href="%s">View sound</a>', 'sound-buttons'),
                get_permalink($result)
            )
        );
    }

    /**
     * Handle bulk upload
     */
    private static function handle_bulk_upload() {
        if (empty($_FILES['bulk_files']) || !is_array($_FILES['bulk_files']['name'])) {
            return array(
                'success' => false,
                'message' => __('No files uploaded.', 'sound-buttons')
            );
        }

        $default_data = array(
            'description' => wp_kses_post($_POST['bulk_description']),
            'categories' => isset($_POST['bulk_categories']) ? array_map('absint', $_POST['bulk_categories']) : array()
        );

        $results = SBP_Upload_Handler::handle_bulk_upload($_FILES['bulk_files'], $default_data);

        $success_count = 0;
        $error_count = 0;
        $details = array();

        foreach ($results as $result) {
            if ($result['success']) {
                $success_count++;
                $details[] = sprintf(__('✓ %s uploaded successfully', 'sound-buttons'), $result['filename']);
            } else {
                $error_count++;
                $details[] = sprintf(__('✗ %s failed: %s', 'sound-buttons'), $result['filename'], $result['error']);
            }
        }

        return array(
            'success' => $success_count > 0,
            'message' => sprintf(
                __('Upload complete: %d succeeded, %d failed', 'sound-buttons'),
                $success_count,
                $error_count
            ),
            'details' => $details
        );
    }
}
