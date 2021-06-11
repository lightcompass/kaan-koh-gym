<?php

/**
 * Template Name: Opening Course
 *
 */
get_header();
?>

<main id="site-content" role="main">

    <?php

    if (have_posts()) {

        while (have_posts()) {
            the_post();
        }
        wp_reset_postdata();
        $args2 = array(
            'paged' => 1,
            'posts_per_page' => '-1',
            'offset' => 0,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 0,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => 'course'
        );
 
        /* The 2nd Query (without global var) */
        $query2 = new WP_Query( $args2 );
        
        // The 2nd Loop
        while ( $query2->have_posts() ) {
            $query2->the_post();
            echo '<div class="course-list">';
            echo get_the_post_thumbnail( get_the_ID(), 'medium' );
            echo "<h2><a href='".get_the_permalink()."'>" . get_the_title( $query2->post->ID ) . "</a></h2>";
            echo get_the_content();
            echo "<a href='".get_the_permalink()."'>Register to this course</a>";
            echo '</div>';
        }
        
        // Restore original Post Data
        wp_reset_postdata();
    }

    ?>

</main><!-- #site-content -->

<?php get_template_part('template-parts/footer-menus-widgets'); ?>

<?php get_footer(); ?>