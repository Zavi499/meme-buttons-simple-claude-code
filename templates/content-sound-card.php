<?php
/**
 * Template for displaying a single sound card
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$audio_url = SBP_Upload_Handler::get_sound_url(get_the_ID());
$variant = get_post_meta(get_the_ID(), '_sbp_button_variant', true);
$is_favorited = SBP_Favorites::is_favorited(get_the_ID());
$favorite_count = SBP_Statistics::get_favorite_count(get_the_ID());
$download_url = SBP_Download_Handler::get_download_url(get_the_ID());

if (!$variant) {
    $variant = rand(1, 10);
}
?>

<div class="sbp-sound-card" data-sound-id="<?php echo esc_attr(get_the_ID()); ?>" data-variant="<?php echo esc_attr($variant); ?>">
    <button class="sbp-play-button sbp-variant-<?php echo esc_attr($variant); ?>"
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

    <h3 class="sbp-sound-title">
        <a href="<?php echo esc_url(get_permalink()); ?>">
            <?php the_title(); ?>
        </a>
    </h3>

    <div class="sbp-sound-actions">
        <button class="sbp-action-btn sbp-favorite-btn <?php echo $is_favorited ? 'is-favorited' : ''; ?>"
                data-sound-id="<?php echo esc_attr(get_the_ID()); ?>"
                title="<?php _e('Favorite', 'sound-buttons'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
            <span class="sbp-favorite-count"><?php echo esc_html($favorite_count); ?></span>
        </button>

        <button class="sbp-action-btn sbp-share-btn"
                data-sound-id="<?php echo esc_attr(get_the_ID()); ?>"
                data-sound-title="<?php echo esc_attr(get_the_title()); ?>"
                data-sound-url="<?php echo esc_url(get_permalink()); ?>"
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
