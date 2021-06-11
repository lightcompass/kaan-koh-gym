<?php

/**
 * Template Name: Right Side Bar
 *
 */
get_header();
?>

<main id="site-content" role="main">
    <div class="col col-left">
        <?php

        if (have_posts()) {

            while (have_posts()) {
                the_post();
                the_content();
            }
        }

        ?>
    </div>
    <div class="col col-right">
        
            <?php dynamic_sidebar('sidebar-1'); ?>
        
    </div>
</main><!-- #site-content -->

<?php get_footer(); ?>