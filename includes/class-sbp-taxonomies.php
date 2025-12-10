<?php
/**
 * Register Custom Taxonomies
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SBP_Taxonomies {

    /**
     * Register the Sound Categories taxonomy
     */
    public static function register() {
        $labels = array(
            'name' => __('Sound Categories', 'sound-buttons'),
            'singular_name' => __('Sound Category', 'sound-buttons'),
            'menu_name' => __('Categories', 'sound-buttons'),
            'all_items' => __('All Categories', 'sound-buttons'),
            'edit_item' => __('Edit Category', 'sound-buttons'),
            'view_item' => __('View Category', 'sound-buttons'),
            'update_item' => __('Update Category', 'sound-buttons'),
            'add_new_item' => __('Add New Category', 'sound-buttons'),
            'new_item_name' => __('New Category Name', 'sound-buttons'),
            'parent_item' => __('Parent Category', 'sound-buttons'),
            'parent_item_colon' => __('Parent Category:', 'sound-buttons'),
            'search_items' => __('Search Categories', 'sound-buttons'),
            'not_found' => __('No categories found', 'sound-buttons'),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'sound-category',
                'with_front' => false,
                'hierarchical' => true
            ),
            'show_in_rest' => true,
        );

        register_taxonomy('sound_category', array('sound'), $args);
    }
}
