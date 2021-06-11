<?php
// for get current url
global $wp;

// perform save data
if ( isset($_GET["confirm"]) && $_GET["confirm"] == '1') {

    add_user_meta( filter_var($_GET["u"], FILTER_SANITIZE_NUMBER_INT), 'registered_course', filter_var($_GET["c"], FILTER_SANITIZE_NUMBER_INT));

    header("Location: " . home_url(). "/thank-you");
}


get_header();
?>

<main id="site-content" role="main">

    <?php

    if (have_posts()) {

        while (have_posts()) {
            the_post();
            $current_user_id = get_current_user_id();
            $current_course_id = get_the_id();
            ?>
            <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
                <div class="post-inner">
                    <div class="entry-content">
                        <h3>Your are registering to...</h3>
                        <?php echo get_the_post_thumbnail( get_the_ID(), 'large' ); ?>
                        <h1><?php echo get_the_title(); ?></h1>
                        <?php
                        the_content();
                        ?>
                        <p><a class="btn" href='<?php echo home_url( $wp->request ) ."?confirm=1&u={$current_user_id}&c={$current_course_id}" ?>'>Confirm</a></p>
                    </div>
                </div>
            </article>
            <?php
        }
    }

    ?>

</main><!-- #site-content -->

<?php get_template_part('template-parts/footer-menus-widgets'); ?>

<?php get_footer(); ?>