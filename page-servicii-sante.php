<?php
/**
 * Simulare Clinica Sante - proiect de pornire.
 * Template automat pentru pagina WordPress cu slug-ul: servicii-sante
 */

get_header();

// Date demonstrative. Preturile sunt fictive, doar pentru exercitiu.
$servicii = array(
    array(
        'cod' => 'cardiologie',
        'nume' => 'Cardiologie',
        'descriere' => 'Consultatii si evaluari ale sanatatii inimii.',
        'durata' => 30,
        'pret' => 200,
        'disponibil' => false,
    ),
    array(
        'cod' => 'dermatologie',
        'nume' => 'Dermatologie',
        'descriere' => 'Consultatii pentru afectiuni ale pielii.',
        'durata' => 25,
        'pret' => 180,
        'disponibil' => true,
    ),
    array(
        'cod' => 'endocrinologie',
        'nume' => 'Endocrinologie',
        'descriere' => 'Evaluarea unor probleme hormonale.',
        'durata' => 40,
        'pret' => 230,
        'disponibil' => true,
    ),
    array(
        'cod' => 'medicina-interna',
        'nume' => 'Medicina interna',
        'descriere' => 'Consultatii generale si evaluari clinice.',
        'durata' => 30,
        'pret' => 190,
        'disponibil' => true,
    ),
    array(
        'cod' => 'neurologie',
        'nume' => 'Neurologie',
        'descriere' => 'Consultații pentru afecțiuni ale sistemului nervos.',
        'durata' => 35,
        'pret' => 210,
        'disponibil' => true,
    ),
);

usort($servicii, function ($a, $b) {
    return $a['pret'] <=> $b['pret'];
});


$articole = new WP_Query(array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 2,
    'orderby' => 'date',
    'order' => 'DESC',
));
?>
<!--
Actualizare specialități (10 minute)
Echipa medicală dorește actualizarea listei de servicii.
- Adaugă specialitatea Neurologie, cu descrierea „Consultații pentru afecțiuni ale sistemului nervos”, durată 35 minute, preț 210 lei, momentan indisponibilă.
- Afișează specialitățile în ordine crescătoare după preț, indiferent de ordinea în care sunt definite în cod.
- Pentru consultațiile cu durata mai mare de 30 de minute, afișează suplimentar textul „Consultație extinsă”.
-->
<main class="container py-5 sante-simulare">
    <div class="mb-4">
        <h1>Servicii si informatii pentru pacienti</h1>
        <p class="lead mb-0">Alege o specialitate si consulta recomandarile noastre.</p>
    </div>

    <section class="mb-5" aria-labelledby="titlu-servicii">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
            <h2 id="titlu-servicii" class="h4 mb-0">Specialitati medicale</h2>

            <div>
                <label for="filtru-specialitate" class="form-label">Filtreaza specialitatile</label>
                <select id="filtru-specialitate" class="form-select">
                    <option value="toate">Toate</option>
                    <option value="disponibile">Disponibile acum</option>
                    <option value="pret-maxim-200">Preț maxim 200 lei</option>
                </select>
            </div>
        </div>

        <div class="row g-4" id="lista-servicii">
            <?php foreach ($servicii as $serviciu) : ?>
                <div class="col-md-6 col-lg-3 serviciu-col"
                     data-disponibil="<?php echo $serviciu['disponibil'] ? '1' : '0'; ?>"
                     data-pret = "<?php echo esc_attr($serviciu['pret']) ?>">
                    <div class="card h-100 sante-serviciu-card">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h3 class="h5 card-title mb-0">
                                    <?php echo esc_html($serviciu['nume']); ?>
                                </h3>
                                <?php if ($serviciu['disponibil']) : ?>
                                    <span class="badge text-bg-success">Disponibil</span>
                                <?php else : ?>
                                    <span class="badge text-bg-secondary">Indisponibil</span>
                                <?php endif; ?>
                            </div>

                            <p class="card-text text-muted">
                                <?php echo esc_html($serviciu['descriere']); ?>
                            </p>
                            <p class="small mb-1">Durata: <?php echo esc_html($serviciu['durata']); ?> min</p>
                            <?php if ($serviciu['durata'] > 30) : ?>
                                <p class="samll text-primary fw-semibold">Consultație extinsă</p>
                            <?php endif; ?>
                            <p class="fw-semibold mt-auto mb-0">
                                Pret orientativ: <?php echo esc_html($serviciu['pret']); ?> lei
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p id="fara-servicii" class="alert alert-warning mt-3 d-none">
            Nu exista servicii pentru filtrul ales.
        </p>
    </section>

    <section class="mb-5" aria-labelledby="titlu-articole">
        <h2 id="titlu-articole" class="h4 mb-3">Articole recente</h2>
        <div class="row g-4">
            <?php if ($articole->have_posts()) : ?>
                <?php while ($articole->have_posts()) : $articole->the_post(); ?>
                    <div class="col-md-6">
                        <article class="card h-100 sante-articol-card">
                            <div class="card-body d-flex flex-column">
                                <h3 class="h5 card-title"><?php the_title(); ?></h3>
                                <p class="small text-muted"><?php echo esc_html(get_the_date()); ?></p>
                                <?php
                                $categorii = get_the_category();

                                if (!empty($categorii)) :
                                ?>
                                    <p class="small text-muted">
                                        Categoria: <?php echo esc_html($categorii[0]->name); ?>
                                    </p>
                                <?php endif; ?>
                                <div class="card-text mb-3"><?php the_excerpt(); ?></div>
                                <a class="btn btn-outline-primary mt-auto"
                                   href="<?php echo esc_url(get_permalink()); ?>">
                                    Citeste articolul
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="col-12"><p class="alert alert-light">Nu exista articole publicate.</p></div>
            <?php endif; ?>
        </div>
    </section>

    <section aria-labelledby="titlu-informatii">
        <h2 id="titlu-informatii" class="h4 mb-3">Informatii utile</h2>
        <button type="button" id="buton-informatii" class="btn btn-primary"
                aria-expanded="false" aria-controls="mesaj-informatii">
            Arata informatii
        </button>
        <div id="mesaj-informatii" class="alert alert-info mt-3" hidden>
            Informatiile sunt orientative. Pentru recomandari personalizate, consultati un medic.
        </div>
    </section>
</main>

<?php get_footer(); ?>
