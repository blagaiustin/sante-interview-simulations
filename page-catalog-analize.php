<?php
/**
 * Pagina de catalog pentru simulare.
 * Slug WordPress: catalog-analize
 * Nu modifica acest fisier in etapa de instalare.
 */
get_header();

$analize = array();
$mod_demo = false;

// Proiectul existent foloseste produsele simple publicate din WooCommerce.
if (function_exists('wc_get_products')) {
    $produse = wc_get_products(array(
        'status'  => 'publish',
        'type'    => 'simple',
        'limit'   => 8,
        'orderby' => 'title',
        'order'   => 'ASC',
    ));

    foreach ($produse as $produs) {
        $analize[] = array(
            'id'          => $produs->get_id(),
            'nume'        => $produs->get_name(),
            'descriere'   => $produs->get_short_description(),
            'pret'        => (float) wc_get_price_to_display($produs),
            'disponibila' => $produs->is_in_stock(),
            'reducere'    => $produs->is_on_sale(),
            'url'         => get_permalink($produs->get_id()),
        );
    }
}

// Daca WooCommerce nu are produse de test, folosim date demo pentru afisare.
if (empty($analize)) {
    $mod_demo = true;
    $analize = array(
        array('id' => 101, 
        'nume' => 'Hemoleucograma', 
        'descriere' => 'Evaluare de baza a celulelor din sange.', 
        'pret' => 55, 
        'disponibila' => true, 
        'reducere' => false, 
        'url' => ''),

        array('id' => 102, 
        'nume' => 'Profil lipidic', 
        'descriere' => 'Analize pentru colesterol si trigliceride.', 
        'pret' => 95, 
        'disponibila' => true, 
        'reducere' => false, 
        'url' => ''),

        array('id' => 103, 
        'nume' => 'Vitamina D', 
        'descriere' => 'Determinarea nivelului vitaminei D.', 
        'pret' => 160, 
        'disponibila' => false, 
        'reducere' => false, 
        'url' => ''),

        array('id' => 104, 
        'nume' => 'Pachet preventie', 
        'descriere' => 'Set de analize pentru evaluarea generala.', 
        'pret' => 250, 
        'disponibila' => true, 
        'reducere' => true, 
        'url' => ''),
    );
}
?>

<main class="container py-5 sante-catalog">
    <div class="row align-items-end g-3 mb-4">
        <div class="col-md-8">
            <h1 class="mb-2">Catalog analize medicale</h1>
            <p class="text-muted mb-0">Consulta preturile si disponibilitatea analizelor.</p>
        </div>
        <div class="col-md-4">
            <label for="sante-filtru-stoc" class="form-label">Afisare analize</label>
            <select id="sante-filtru-stoc" class="form-select">
                <option value="toate">Toate</option>
                <option value="disponibile">Doar disponibile</option>
            </select>
        </div>
    </div>

    <?php if ($mod_demo) : ?>
        <div class="alert alert-info small" role="status">
            Mod demonstrativ: sunt afisate analize de exemplu. Daca exista produse simple publicate in WooCommerce, vor fi afisate automat acelea.
        </div>
    <?php endif; ?>

    <div id="sante-lista-analize" class="row g-4">
        <?php foreach ($analize as $analiza) : ?>
            <div class="col-12 col-md-6 col-lg-3 sante-col-analiza"
                 data-disponibila="<?php echo $analiza['disponibila'] ? '1' : '0'; ?>"
                 data-pret="<?php echo esc_attr($analiza['pret']); ?>">
                <article class="card h-100 sante-card-analiza">
                    <div class="card-body d-flex flex-column">
                        <h2 class="h5 card-title"><?php echo esc_html($analiza['nume']); ?></h2>
                        <p class="small text-muted flex-grow-1">
                            <?php echo esc_html(wp_strip_all_tags($analiza['descriere'])); ?>
                        </p>
                        <div class="mb-2">
                            <?php if ($analiza['disponibila']) : ?>
                                <span class="badge bg-success">Disponibila</span>
                            <?php else : ?>
                                <span class="badge bg-secondary">Indisponibila</span>
                            <?php endif; ?>
                        </div>
                        <div class="sante-pret mb-3">
                            <?php
                            if (function_exists('wc_price')) {
                                echo wp_kses_post(wc_price($analiza['pret']));
                            } else {
                                echo esc_html(number_format_i18n($analiza['pret'], 2) . ' lei');
                            }
                            ?>
                        </div>
                        <?php if (!$mod_demo) : ?>
                            <a class="btn btn-outline-primary mt-auto" href="<?php echo esc_url($analiza['url']); ?>">
                                Vezi analiza
                            </a>
                        <?php else : ?>
                            <span class="text-muted small">Produs demonstrativ</span>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="sante-fara-rezultate" class="alert alert-warning mt-4 d-none" role="status">
        Nu exista analize care corespund filtrului.
    </div>
</main>

<?php get_footer(); ?>
