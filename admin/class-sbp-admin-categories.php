<?php
/**
 * Admin category management
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SBP_Admin_Categories {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('sound_category_add_form_fields', array($this, 'add_category_fields'));
        add_action('sound_category_edit_form_fields', array($this, 'edit_category_fields'), 10, 2);
        add_action('created_sound_category', array($this, 'save_category_fields'));
        add_action('edited_sound_category', array($this, 'save_category_fields'));
        add_filter('manage_edit-sound_category_columns', array($this, 'category_columns'));
        add_filter('manage_sound_category_custom_column', array($this, 'category_column_content'), 10, 3);
    }

    /**
     * Add category custom fields
     */
    public function add_category_fields() {
        ?>
        <div class="form-field">
            <label for="category_color"><?php _e('Category Color', 'sound-buttons'); ?></label>
            <input type="text" id="category_color" name="category_color" value="#6366f1" class="sbp-color-picker">
            <p><?php _e('Choose a color for this category (used for visual styling).', 'sound-buttons'); ?></p>
        </div>
        <?php
    }

    /**
     * Edit category custom fields
     */
    public function edit_category_fields($term, $taxonomy) {
        $color = get_term_meta($term->term_id, 'category_color', true);
        if (!$color) {
            $color = '#6366f1';
        }
        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="category_color"><?php _e('Category Color', 'sound-buttons'); ?></label>
            </th>
            <td>
                <input type="text" id="category_color" name="category_color" value="<?php echo esc_attr($color); ?>" class="sbp-color-picker">
                <p class="description"><?php _e('Choose a color for this category.', 'sound-buttons'); ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * Save category custom fields
     */
    public function save_category_fields($term_id) {
        if (isset($_POST['category_color'])) {
            update_term_meta($term_id, 'category_color', sanitize_hex_color($_POST['category_color']));
        }
    }

    /**
     * Add custom columns to category list
     */
    public function category_columns($columns) {
        $new_columns = array();

        foreach ($columns as $key => $column) {
            $new_columns[$key] = $column;

            if ($key === 'name') {
                $new_columns['color'] = __('Color', 'sound-buttons');
                $new_columns['sound_count'] = __('Sounds', 'sound-buttons');
            }
        }

        return $new_columns;
    }

    /**
     * Display custom column content
     */
    public function category_column_content($content, $column_name, $term_id) {
        if ($column_name === 'color') {
            $color = get_term_meta($term_id, 'category_color', true);
            if ($color) {
                $content = '<span style="display: inline-block; width: 20px; height: 20px; background-color: ' . esc_attr($color) . '; border: 1px solid #ddd; border-radius: 3px;"></span>';
            }
        }

        if ($column_name === 'sound_count') {
            $count = wp_count_terms(array(
                'taxonomy' => 'sound_category',
                'parent' => $term_id,
                'hide_empty' => false
            ));

            $content = number_format_i18n($count);
        }

        return $content;
    }
}

// Initialize
new SBP_Admin_Categories();
