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

            <div class="sbp-sound-layout">
                <!-- Left: Play Button -->
                <div class="sbp-sound-player">
                    <button class="sbp-play-button-3d sbp-play-button-large sbp-variant-<?php echo esc_attr($variant); ?>"
                            data-audio-url="<?php echo esc_url($audio_url); ?>"
                            data-sound-id="<?php echo esc_attr(get_the_ID()); ?>"
                            aria-label="<?php echo esc_attr(sprintf(__('Play %s', 'sound-buttons'), get_the_title())); ?>">
                        <div class="sbp-button-3d-base"></div>
                        <div class="sbp-button-3d-top">
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
                        </div>
                    </button>
                </div>

                <!-- Right: Content -->
                <div class="sbp-sound-details">
                    <h1 class="sbp-sound-title-large"><?php the_title(); ?></h1>

                    <!-- Action Buttons Grid (2x2) -->
                    <div class="sbp-action-buttons-grid">
                        <button class="sbp-action-button sbp-btn-red sbp-favorite-btn-large <?php echo $is_favorited ? 'is-favorited' : ''; ?>"
                                data-sound-id="<?php echo esc_attr(get_the_ID()); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span><?php _e('Add to My Soundboard', 'sound-buttons'); ?></span>
                        </button>

                        <a href="<?php echo esc_url($download_url); ?>" class="sbp-action-button sbp-btn-green">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            <span><?php _e('Get Ringtone', 'sound-buttons'); ?></span>
                        </a>

                        <a href="<?php echo esc_url($download_url); ?>" class="sbp-action-button sbp-btn-blue">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                            </svg>
                            <span><?php _e('Download MP3', 'sound-buttons'); ?></span>
                        </a>

                        <a href="<?php echo esc_url($download_url); ?>" class="sbp-action-button sbp-btn-yellow">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/>
                            </svg>
                            <span><?php _e('Notification Sound', 'sound-buttons'); ?></span>
                        </a>
                    </div>

                    <!-- Social Media Sharing -->
                    <div class="sbp-social-sharing">
                        <h3 class="sbp-social-title"><?php _e('Social Media Sharing', 'sound-buttons'); ?></h3>
                        <div class="sbp-social-buttons">
                            <button class="sbp-social-icon sbp-social-facebook" data-share="facebook"
                                    data-url="<?php echo esc_url(get_permalink()); ?>"
                                    data-title="<?php echo esc_attr(get_the_title()); ?>">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                                </svg>
                            </button>
                            <button class="sbp-social-icon sbp-social-twitter" data-share="twitter"
                                    data-url="<?php echo esc_url(get_permalink()); ?>"
                                    data-title="<?php echo esc_attr(get_the_title()); ?>">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                                </svg>
                            </button>
                            <button class="sbp-social-icon sbp-social-whatsapp" data-share="whatsapp"
                                    data-url="<?php echo esc_url(get_permalink()); ?>"
                                    data-title="<?php echo esc_attr(get_the_title()); ?>">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                            </button>
                            <button class="sbp-social-icon sbp-social-copy" data-share="copy"
                                    data-url="<?php echo esc_url(get_permalink()); ?>">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Statistics Cards (Full Width Below) -->
            <div class="sbp-statistics-cards">
                <div class="sbp-stat-card sbp-stat-favorites">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <div class="sbp-stat-content">
                        <div class="sbp-stat-number"><?php echo number_format_i18n($favorite_count); ?></div>
                        <div class="sbp-stat-label"><?php _e('favorited this sound button', 'sound-buttons'); ?></div>
                    </div>
                </div>

                <div class="sbp-stat-card sbp-stat-views">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                    </svg>
                    <div class="sbp-stat-content">
                        <div class="sbp-stat-number"><?php echo number_format_i18n($play_count); ?></div>
                        <div class="sbp-stat-label"><?php _e('viewed this sound button', 'sound-buttons'); ?></div>
                    </div>
                </div>

                <div class="sbp-stat-card sbp-stat-plays">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    <div class="sbp-stat-content">
                        <div class="sbp-stat-number"><?php echo number_format_i18n($play_count); ?></div>
                        <div class="sbp-stat-label"><?php _e('played this sound button', 'sound-buttons'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <?php if (get_the_content()): ?>
                <div class="sbp-sound-description">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>

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
