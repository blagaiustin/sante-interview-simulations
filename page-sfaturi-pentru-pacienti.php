<?php
get_header();
?>

<?php

$args = array(
'post_type' => 'post',
'posts_per_page' => 4,
'orderby' => 'date',
'order' => 'DESC',
);

$query = new WP_Query( $args );

?>

<div class="container py-5 text-center">

    <h1>Sfaturi pentru pacienti</h1>

    <div class="row g-4">

    <?php if($query->have_posts() ) {

        while($query->have_posts() ) {

            $query->the_post();?>

            <div class="col-md-6">

                <div class="card h-100 soft-card">

                    <div class="card-body">

                        <h5 class="card-title"><?php the_title(); ?></h5>

                        <p class="card-text"><?php echo get_the_date(); ?></p>

                        <div class="card-text"><?php the_excerpt(); ?></div>

                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">Vezi articolul</a>

                    </div>

                </div>

            </div>

            <?php
                }

            wp_reset_postdata();

            } else {
                echo '<p>Momentan nu exista articole disponibile.</p>';
            }
            ?>
    </div>

</div>

<div class="container py-5 text-center">

<button id="vezi-averitisment" class="btn btn-primary">
    Vezi avertisment!
</button>

<p id="mesaj-avertisment" style="display: none;">
    Informatiile prezentate nu inlocuiesc sfatul unui medic.
</p>

</div>



        




<?php
get_footer();
?>