<?php
/**
 * Public-facing functionality
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SBP_Public {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_head', array($this, 'add_inline_styles'));
        add_shortcode('sound_buttons', array($this, 'sound_buttons_shortcode'));
        add_shortcode('trending_sounds', array($this, 'trending_sounds_shortcode'));
    }

    /**
     * Add inline styles for dynamic content
     */
    public function add_inline_styles() {
        // Add any dynamic CSS here if needed
    }

    /**
     * Sound buttons shortcode
     *
     * Usage: [sound_buttons category="game-sounds" limit="12"]
     */
    public function sound_buttons_shortcode($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'limit' => 30,
            'orderby' => 'date',
            'order' => 'DESC'
        ), $atts);

        $args = array(
            'post_type' => 'sound',
            'posts_per_page' => absint($atts['limit']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order']
        );

        if (!empty($atts['category'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'sound_category',
                    'field' => 'slug',
                    'terms' => $atts['category']
                )
            );
        }

        $query = new WP_Query($args);

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="sbp-sound-grid">';

            while ($query->have_posts()) {
                $query->the_post();
                SBP_Template_Loader::get_template_part('content', 'sound-card');
            }

            echo '</div>';
        } else {
            echo '<p>' . __('No sounds found.', 'sound-buttons') . '</p>';
        }

        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Trending sounds shortcode
     *
     * Usage: [trending_sounds limit="10"]
     */
    public function trending_sounds_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 10,
            'days' => 7
        ), $atts);

        $trending_ids = SBP_Statistics::get_trending_sounds(absint($atts['limit']), absint($atts['days']));

        if (empty($trending_ids)) {
            return '<p>' . __('No trending sounds found.', 'sound-buttons') . '</p>';
        }

        $args = array(
            'post_type' => 'sound',
            'post__in' => $trending_ids,
            'orderby' => 'post__in',
            'posts_per_page' => absint($atts['limit'])
        );

        $query = new WP_Query($args);

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="sbp-sound-grid sbp-trending-sounds">';
            echo '<h2 class="sbp-section-title">' . __('Trending Sounds', 'sound-buttons') . '</h2>';

            while ($query->have_posts()) {
                $query->the_post();
                SBP_Template_Loader::get_template_part('content', 'sound-card');
            }

            echo '</div>';
        }

        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Get sound button HTML
     *
     * @param int $post_id Sound post ID
     * @return string HTML output
     */
    public static function get_sound_button($post_id) {
        $audio_url = SBP_Upload_Handler::get_sound_url($post_id);
        $variant = get_post_meta($post_id, '_sbp_button_variant', true);
        $is_favorited = SBP_Favorites::is_favorited($post_id);
        $play_count = SBP_Statistics::get_play_count($post_id);
        $favorite_count = SBP_Statistics::get_favorite_count($post_id);
        $download_url = SBP_Download_Handler::get_download_url($post_id);

        if (!$variant) {
            $variant = 1;
        }

        ob_start();
        ?>
        <div class="sbp-sound-card" data-sound-id="<?php echo esc_attr($post_id); ?>" data-variant="<?php echo esc_attr($variant); ?>">
            <button class="sbp-play-button sbp-variant-<?php echo esc_attr($variant); ?>"
                    data-audio-url="<?php echo esc_url($audio_url); ?>"
                    data-sound-id="<?php echo esc_attr($post_id); ?>"
                    aria-label="<?php echo esc_attr(sprintf(__('Play %s', 'sound-buttons'), get_the_title($post_id))); ?>">
                <span class="sbp-play-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </span>
                <span class="sbp-pause-icon" style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                    </svg>
                </span>
            </button>

            <h3 class="sbp-sound-title">
                <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                    <?php echo esc_html(get_the_title($post_id)); ?>
                </a>
            </h3>

            <div class="sbp-sound-actions">
                <button class="sbp-action-btn sbp-favorite-btn <?php echo $is_favorited ? 'is-favorited' : ''; ?>"
                        data-sound-id="<?php echo esc_attr($post_id); ?>"
                        title="<?php _e('Favorite', 'sound-buttons'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <span class="sbp-favorite-count"><?php echo esc_html($favorite_count); ?></span>
                </button>

                <button class="sbp-action-btn sbp-share-btn"
                        data-sound-id="<?php echo esc_attr($post_id); ?>"
                        data-sound-title="<?php echo esc_attr(get_the_title($post_id)); ?>"
                        data-sound-url="<?php echo esc_url(get_permalink($post_id)); ?>"
                        title="<?php _e('Share', 'sound-buttons'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/>
                    </svg>
                </button>

                <a href="<?php echo esc_url($download_url); ?>"
                   class="sbp-action-btn sbp-download-btn"
                   title="<?php _e('Download', 'sound-buttons'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                    </svg>
                </a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialize public class
new SBP_Public();
