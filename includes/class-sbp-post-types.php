<?php
/**
 * Register Custom Post Types
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SBP_Post_Types {

    /**
     * Register the Sounds custom post type
     */
    public static function register() {
        $labels = array(
            'name' => __('Sounds', 'sound-buttons'),
            'singular_name' => __('Sound', 'sound-buttons'),
            'menu_name' => __('Sound Buttons', 'sound-buttons'),
            'add_new' => __('Add New Sound', 'sound-buttons'),
            'add_new_item' => __('Add New Sound', 'sound-buttons'),
            'edit_item' => __('Edit Sound', 'sound-buttons'),
            'new_item' => __('New Sound', 'sound-buttons'),
            'view_item' => __('View Sound', 'sound-buttons'),
            'search_items' => __('Search Sounds', 'sound-buttons'),
            'not_found' => __('No sounds found', 'sound-buttons'),
            'not_found_in_trash' => __('No sounds found in trash', 'sound-buttons'),
            'all_items' => __('All Sounds', 'sound-buttons'),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'sound',
                'with_front' => false
            ),
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-controls-play',
            'supports' => array('title', 'editor', 'thumbnail'),
            'show_in_rest' => true,
        );

        register_post_type('sound', $args);
    }
}
