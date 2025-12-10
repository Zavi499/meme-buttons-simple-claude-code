<?php
/**
 * Template for displaying all sounds archive
 */

get_header();
?>

<div class="sbp-sound-archive-wrapper">
    <div class="sbp-container">

        <!-- Breadcrumb -->
        <nav class="sbp-breadcrumb" aria-label="<?php _e('Breadcrumb', 'sound-buttons'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'sound-buttons'); ?></a>
            <span class="sbp-breadcrumb-separator">&gt;</span>
            <span class="sbp-breadcrumb-current"><?php _e('All Sounds', 'sound-buttons'); ?></span>
        </nav>

        <!-- Archive Header -->
        <header class="sbp-archive-header">
            <h1 class="sbp-archive-title"><?php _e('All Sound Buttons', 'sound-buttons'); ?></h1>
            <p class="sbp-archive-description">
                <?php _e('Browse our complete collection of sound buttons.', 'sound-buttons'); ?>
            </p>
        </header>

        <!-- Filter by Category (Optional) -->
        <?php
        $categories = get_terms(array(
            'taxonomy' => 'sound_category',
            'hide_empty' => true
        ));

        if ($categories && !is_wp_error($categories)):
        ?>
            <div class="sbp-category-filter">
                <a href="<?php echo esc_url(get_post_type_archive_link('sound')); ?>"
                   class="sbp-filter-btn <?php echo !is_tax() ? 'active' : ''; ?>">
                    <?php _e('All Categories', 'sound-buttons'); ?>
                </a>
                <?php foreach ($categories as $category): ?>
                    <a href="<?php echo esc_url(get_term_link($category)); ?>"
                       class="sbp-filter-btn">
                        <?php echo esc_html($category->name); ?>
                        <span class="sbp-filter-count"><?php echo esc_html($category->count); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

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
                <p><?php _e('No sounds found.', 'sound-buttons'); ?></p>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
