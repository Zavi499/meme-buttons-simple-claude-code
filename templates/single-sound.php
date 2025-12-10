<?php
/**
 * Template for displaying single sound page
 */

get_header();

$audio_url = SBP_Upload_Handler::get_sound_url(get_the_ID());
$variant = get_post_meta(get_the_ID(), '_sbp_button_variant', true);
$is_favorited = SBP_Favorites::is_favorited(get_the_ID());
$play_count = SBP_Statistics::get_play_count(get_the_ID());
$favorite_count = SBP_Statistics::get_favorite_count(get_the_ID());
$download_url = SBP_Download_Handler::get_download_url(get_the_ID());

if (!$variant) {
    $variant = 1;
}

$categories = get_the_terms(get_the_ID(), 'sound_category');
$primary_category = $categories && !is_wp_error($categories) ? $categories[0] : null;
?>

<div class="sbp-single-sound-wrapper">
    <div class="sbp-container">

        <!-- Breadcrumb -->
        <nav class="sbp-breadcrumb" aria-label="<?php _e('Breadcrumb', 'sound-buttons'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'sound-buttons'); ?></a>
            <?php if ($primary_category): ?>
                <span class="sbp-breadcrumb-separator">&gt;</span>
                <a href="<?php echo esc_url(get_term_link($primary_category)); ?>">
                    <?php echo esc_html($primary_category->name); ?>
                </a>
            <?php endif; ?>
            <span class="sbp-breadcrumb-separator">&gt;</span>
            <span class="sbp-breadcrumb-current"><?php the_title(); ?></span>
        </nav>

        <!-- Main Sound Section -->
        <article id="post-<?php the_ID(); ?>" <?php post_class('sbp-single-sound'); ?>>

            <header class="sbp-sound-header">
                <h1 class="sbp-sound-title"><?php the_title(); ?></h1>

                <?php if ($categories && !is_wp_error($categories)): ?>
                    <div class="sbp-sound-categories">
                        <?php foreach ($categories as $category): ?>
                            <a href="<?php echo esc_url(get_term_link($category)); ?>" class="sbp-category-tag">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </header>

            <div class="sbp-sound-content">

                <!-- Large Play Button -->
                <div class="sbp-sound-player">
                    <button class="sbp-play-button sbp-play-button-large sbp-variant-<?php echo esc_attr($variant); ?>"
                            data-audio-url="<?php echo esc_url($audio_url); ?>"
                            data-sound-id="<?php echo esc_attr(get_the_ID()); ?>"
                            aria-label="<?php echo esc_attr(sprintf(__('Play %s', 'sound-buttons'), get_the_title())); ?>">
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
                </div>

                <!-- Action Buttons Grid -->
                <div class="sbp-action-buttons-grid">
                    <button class="sbp-action-button sbp-favorite-btn-large <?php echo $is_favorited ? 'is-favorited' : ''; ?>"
                            data-sound-id="<?php echo esc_attr(get_the_ID()); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        <span><?php _e('Add to My Soundboard', 'sound-buttons'); ?></span>
                    </button>

                    <a href="<?php echo esc_url($download_url); ?>" class="sbp-action-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span><?php _e('Use as Ringtone', 'sound-buttons'); ?></span>
                    </a>

                    <a href="<?php echo esc_url($download_url); ?>" class="sbp-action-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/>
                        </svg>
                        <span><?php _e('Use as Notification Tone', 'sound-buttons'); ?></span>
                    </a>

                    <a href="<?php echo esc_url($download_url); ?>" class="sbp-action-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                        </svg>
                        <span><?php _e('Download MP3', 'sound-buttons'); ?></span>
                    </a>
                </div>

                <!-- Statistics -->
                <div class="sbp-sound-statistics">
                    <div class="sbp-stat-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        <span><?php printf(__('%s users played this sound', 'sound-buttons'), '<strong>' . number_format_i18n($play_count) . '</strong>'); ?></span>
                    </div>
                    <div class="sbp-stat-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        <span><?php printf(__('%s users added to favorites', 'sound-buttons'), '<strong>' . number_format_i18n($favorite_count) . '</strong>'); ?></span>
                    </div>
                </div>

                <!-- Description -->
                <?php if (get_the_content()): ?>
                    <div class="sbp-sound-description">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>

            </div>

        </article>

        <!-- Related Sounds Section -->
        <?php if ($primary_category): ?>
            <section class="sbp-related-sounds">
                <h2 class="sbp-section-title"><?php _e('You Might Also Like', 'sound-buttons'); ?></h2>

                <?php
                $related_args = array(
                    'post_type' => 'sound',
                    'posts_per_page' => 30,
                    'post__not_in' => array(get_the_ID()),
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'sound_category',
                            'field' => 'term_id',
                            'terms' => $primary_category->term_id
                        )
                    ),
                    'orderby' => 'rand'
                );

                $related_query = new WP_Query($related_args);

                if ($related_query->have_posts()):
                ?>
                    <div class="sbp-sound-grid">
                        <?php
                        while ($related_query->have_posts()):
                            $related_query->the_post();
                            SBP_Template_Loader::get_template_part('content', 'sound-card');
                        endwhile;
                        ?>
                    </div>

                    <div class="sbp-view-more">
                        <a href="<?php echo esc_url(get_term_link($primary_category)); ?>" class="sbp-button sbp-button-secondary">
                            <?php _e('View More Sounds', 'sound-buttons'); ?>
                        </a>
                    </div>
                <?php
                endif;
                wp_reset_postdata();
                ?>

            </section>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
