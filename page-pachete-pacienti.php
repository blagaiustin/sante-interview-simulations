<?php
/**
 * Template incarcat automat pentru pagina cu slug-ul pachete-pacienti.
 * Simulare de interventie pe un proiect WordPress existent.
 */
defined('ABSPATH') || exit;

require_once get_stylesheet_directory() . '/inc/sante-sim4-catalog.php';
require_once get_stylesheet_directory() . '/inc/sante-sim4-components.php';

$catalog = sante_sim4_load_catalog();
$analize = $catalog['analize'];
$mod_demo = $catalog['demo'];
$categoriile = sante_sim4_categories($analize);
$nr_total = count($analize);

get_header();
?>

<main class="container py-5 sante4" id="sante4-app">
    <header class="sante4-hero mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <p class="sante4-eyebrow mb-2">Clinica Sante · mediu de test</p>
                <h1 class="mb-2">Analize si pachete medicale</h1>
                <p class="lead text-muted mb-0">Gaseste rapid investigatiile potrivite si verifica disponibilitatea.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="sante4-summary">
                    <strong id="sante4-count"><?php echo esc_html($nr_total); ?></strong>
                    <span id="sante4-count-label">rezultate afisate</span>
                </span>
            </div>
        </div>
    </header>

    <?php if ($mod_demo) : ?>
        <div class="alert alert-info small" role="status">
            Date demonstrative. Pentru produse reale foloseste WooCommerce; un administrator poate forta demo prin ?sante_demo=1.
        </div>
    <?php endif; ?>

    <?php sante_sim4_render_filters($categoriile); ?>

    <section class="sante4-results" aria-label="Lista analizelor">
        <div class="row g-4" id="sante4-list">
            <?php foreach ($analize as $analiza) : ?>
                <?php sante_sim4_render_card($analiza, $mod_demo); ?>
            <?php endforeach; ?>
        </div>

        <div id="sante4-empty" class="alert alert-warning mt-4 d-none" role="status">
            Nu exista rezultate pentru filtrele selectate.
        </div>
    </section>

    <?php sante_sim4_render_help(); ?>
</main>

<?php get_footer(); ?>
