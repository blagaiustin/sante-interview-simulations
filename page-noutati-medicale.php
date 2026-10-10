<?php
get_header();
?>

<!-- container py-5 sunt două clase Bootstrap diferite puse pe același <div>
   container ne centreaza elementele de mai jos pe pagina si py-5 face padding pe verticală
   ca sa nu intram cu textul in divul de mai jos-->
<div class="container py-5">
    <h1>
    Noutăți medicale   
    </h1>

    <!-- clasa lead evidentiaza un paragraf. E tot o clasa bootstrap -->
    <p class="lead">
    Cele mai recente informații și recomandări pentru pacienți.
    </p>
</div>

<?php
//ultimele 3 postari wordpress dupa data si descrescator
$args = array (
    'post_type' => 'post',
    'posts_per_page' => 3,
    'orderby' => 'date',
    'order' => 'DESC',
);

$query = new WP_Query($args);
?>

<div class="container">
    <div class="row g-4 noutati-row">
        
        <?php
        if ($query->have_posts()) {
           
            //creez un loop
            while($query->have_posts()) {
                $query->the_post();
                ?>

                <!--card bootstrap-->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                        
                            <!--titlu-->
                            <h5 class="card-title"><?php the_title(); ?></h5>

                            <!--data-->
                            <p class="text-muted"><?php echo get_the_date(); ?></p>

                            <!--excerpt-->
                            <div class="card-text"> <?php the_excerpt(); ?></div>

                            <!--link-->
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">Citeste articolul</a>
                        </div>
                    </div>
                </div>

                <?php
            }

            wp_reset_postdata();
            
        } else {
            echo '<p>Momentan nu exista articole disponibile</p>';

        }

        ?>

    </div>

</div>

<!--
Creem buton: „Arată informații” e cu event trebuie sa se incarce in browser fara refresh la click
cand apas pe el sa apara: Aceste informații au caracter orientativ și nu înlocuiesc consultația medicală.
caut dupa ajax post within jquery onclick. trebuie sa pun cod in custom.js
-->
<div class="container py-5 text-center">

    <button id="arata-informatii" class="btn btn-primary">Arată informații</button>

    <p id="mesaj-informatii" style="display: none;">Aceste informații au caracter orientativ și nu înlocuiesc consultația medicală.</p>


</div>

<?php get_footer();?>
