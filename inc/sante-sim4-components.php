<?php
/**
 * Simulare Sante - componente HTML reutilizabile pentru pagina catalog.
 * Nu se modifica template-ul principal pentru fiecare detaliu vizual.
 */
defined('ABSPATH') || exit;

if (!function_exists('sante_sim4_render_filters')) {
    function sante_sim4_render_filters($categorii) {
        ?>
        <section class="sante4-toolbar mb-4" aria-label="Filtrare analize">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-5">
                    <label class="form-label" for="sante4-cautare">Cauta o analiza</label>
                    <input id="sante4-cautare" type="search" class="form-control" placeholder="Ex.: TSH, glicemie..." autocomplete="off">
                </div>
                <div class="col-6 col-lg-3">
                    <label class="form-label" for="sante4-categorie">Categorie</label>
                    <select id="sante4-categorie" class="form-select">
                        <option value="toate">Toate categoriile</option>
                        <?php foreach ($categorii as $categorie) : ?>
                            <option value="<?php echo esc_attr(sanitize_title($categorie)); ?>">
                                <?php echo esc_html($categorie); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label" for="sante4-stoc">Disponibilitate</label>
                    <select id="sante4-stoc" class="form-select">
                        <option value="toate">Toate</option>
                        <option value="disponibile">Disponibile</option>
                    </select>
                </div>
                <div class="col-12 col-lg-2">
                    <button type="button" id="sante4-reset" class="btn btn-outline-secondary w-100">Reseteaza</button>
                </div>
            </div>
        </section>
        <?php
    }
}

if (!function_exists('sante_sim4_render_card')) {
    function sante_sim4_render_card($analiza, $mod_demo) {
        $disponibila = (bool) $analiza['disponibila'];
        $este_promotie = (bool) $analiza['promotie'];
        $slug_categorie = sanitize_title($analiza['categorie']);
        ?>
        <div class="col-12 col-md-6 col-xl-4 sante4-item"
             data-nume="<?php echo esc_attr(remove_accents(mb_strtolower($analiza['nume']))); ?>"
             data-categorie="<?php echo esc_attr($slug_categorie); ?>"
             data-disponibila="<?php echo $disponibila ? '1' : '0'; ?>"
             data-promotie="<?php echo $este_promotie ? '1' : '0'; ?>"
             data-pret="<?php echo esc_attr($analiza['pret']); ?>">
            <article class="card sante4-card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <h3 class="h5 card-title mb-0"><?php echo esc_html($analiza['nume']); ?></h3>
                        <?php if ($este_promotie) : ?>
                            <span class="badge text-bg-warning">Oferta</span>
                        <?php endif; ?>
                    </div>

                    <p class="sante4-category mb-2"><?php echo esc_html($analiza['categorie']); ?></p>
                    <p class="sante4-description flex-grow-1">
                        <?php echo esc_html(wp_strip_all_tags($analiza['descriere'])); ?>
                    </p>

                    <div class="d-flex align-items-center justify-content-between mb-3 gap-2">
                        <?php if ($disponibila) : ?>
                            <span class="badge bg-success">Disponibila</span>
                        <?php else : ?>
                            <span class="badge bg-secondary">Indisponibila</span>
                        <?php endif; ?>
                        <span class="sante4-duration small text-muted">
                            <?php echo esc_html($analiza['durata']); ?> min
                        </span>
                    </div>

                    <div class="sante4-pricing mb-3">
                        <?php if ($este_promotie && $analiza['pret_vechi'] > $analiza['pret']) : ?>
                            <span class="sante4-old-price">
                                <?php echo wp_kses_post(sante_sim4_format_price($analiza['pret_vechi'])); ?>
                            </span>
                        <?php endif; ?>
                        <strong class="sante4-price">
                            <?php echo wp_kses_post(sante_sim4_format_price($analiza['pret'])); ?>
                        </strong>
                    </div>

                    <?php if (!$disponibila) : ?>
                        <button type="button" class="btn btn-outline-secondary mt-auto" disabled>
                            Momentan indisponibila
                        </button>
                    <?php elseif (!$mod_demo && !empty($analiza['url'])) : ?>
                        <a class="btn btn-outline-primary mt-auto" href="<?php echo esc_url($analiza['url']); ?>">
                            Vezi analiza
                        </a>
                    <?php else : ?>
                        <span class="small text-muted mt-auto">Exemplu demonstrativ</span>
                    <?php endif; ?>
                </div>
            </article>
        </div>
        <?php
    }
}

if (!function_exists('sante_sim4_render_help')) {
    function sante_sim4_render_help() {
        ?>
        <aside class="sante4-help mt-5" aria-label="Informatii utile">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <h2 class="h4">Informatii utile</h2>
                    <p class="text-muted mb-3">Preturile sunt orientative. Disponibilitatea poate diferi in functie de centrul de recoltare.</p>
                    <button type="button" class="btn btn-link px-0" id="sante4-ajutor" aria-expanded="false" aria-controls="sante4-raspuns">
                        Cum aleg o analiza?
                    </button>
                    <div id="sante4-raspuns" hidden>
                        <p class="mb-0">Daca nu stii ce investigatii ai nevoie, cere recomandarea medicului tau.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="sante4-note p-3">
                        <strong>Nota</strong>
                        <p class="mb-0 small">Paginile de test nu plaseaza comenzi si nu proceseaza plati.</p>
                    </div>
                </div>
            </div>
        </aside>
        <?php
    }
}
