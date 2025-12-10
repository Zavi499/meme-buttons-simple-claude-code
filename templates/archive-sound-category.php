<?php
/**
 * Template for displaying sound category archive
 */

get_header();

$term = get_queried_object();
$sounds_per_page = get_option('sbp_sounds_per_page', 30);
?>

<div class="sbp-category-archive-wrapper">
    <div class="sbp-container">

        <!-- Breadcrumb -->
        <nav class="sbp-breadcrumb" aria-label="<?php _e('Breadcrumb', 'sound-buttons'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'sound-buttons'); ?></a>
            <span class="sbp-breadcrumb-separator">&gt;</span>
            <span class="sbp-breadcrumb-current"><?php echo esc_html($term->name); ?></span>
        </nav>

        <!-- Archive Header -->
        <header class="sbp-archive-header">
            <h1 class="sbp-archive-title"><?php echo esc_html($term->name); ?></h1>

            <?php if ($term->description): ?>
                <div class="sbp-archive-description">
                    <?php echo wp_kses_post($term->description); ?>
                </div>
            <?php endif; ?>

            <div class="sbp-archive-meta">
                <?php
                $count = $term->count;
                printf(
                    _n('%s sound', '%s sounds', $count, 'sound-buttons'),
                    '<strong>' . number_format_i18n($count) . '</strong>'
                );
                ?>
            </div>
        </header>

        <!-- Sounds Grid -->
        <?php if (have_posts()): ?>
            <div class="sbp-sound-grid">
                <?php
                while (have_posts()):
                    the_post();
                    SBP_Template_Loader::get_template_part('content', 'sound-card');
                endwhile;
                ?>
            </div>

            <!-- Pagination -->
            <?php
            $big = 999999999;
            $pagination = paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $GLOBALS['wp_query']->max_num_pages,
                'prev_text' => __('&laquo; Previous', 'sound-buttons'),
                'next_text' => __('Next &raquo;', 'sound-buttons'),
                'type' => 'plain'
            ));

            if ($pagination):
            ?>
                <nav class="sbp-pagination" aria-label="<?php _e('Pagination', 'sound-buttons'); ?>">
                    <?php echo $pagination; ?>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="sbp-no-sounds">
                <p><?php _e('No sounds found in this category.', 'sound-buttons'); ?></p>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
