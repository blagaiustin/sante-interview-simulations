<?php
get_header();

if ( have_posts() ) {

    while ( have_posts() ) {

        the_post();

        echo '<h1>';
        the_title();
        echo '</h1>';

        the_content();

        echo '<hr>';
        echo '<h2>Ultimele articole</h2>';

        $articole = new WP_Query([
            'orderby' => 'title',
            'order'   => 'ASC'
        ]);

        if ( $articole->have_posts() ) {

            while ( $articole->have_posts() ) {

                $articole->the_post();

                echo '<h3>';
                the_title();
                echo '</h3>';
            }

        }

        wp_reset_postdata();
            }

}

get_footer();