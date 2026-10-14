<?php
/**
 * Simulare Sante - stratul de date.
 * Acest fisier nu afiseaza HTML; pregateste datele pentru template.
 */
defined('ABSPATH') || exit;

if (!function_exists('sante_sim4_demo_data')) {
    function sante_sim4_demo_data() {
        return array(
            array('id'=>201, 'nume'=>'Hemoleucograma completa', 'categorie'=>'Analize uzuale', 'descriere'=>'Investigatie de rutina pentru evaluarea celulelor sanguine.', 'pret'=>48, 'pret_vechi'=>48, 'disponibila'=>true, 'promotie'=>false, 'durata'=>15, 'url'=>''),
            array('id'=>202, 'nume'=>'Profil lipidic', 'categorie'=>'Analize uzuale', 'descriere'=>'Colesterol total, HDL, LDL si trigliceride.', 'pret'=>89, 'pret_vechi'=>105, 'disponibila'=>true, 'promotie'=>true, 'durata'=>15, 'url'=>''),
            array('id'=>203, 'nume'=>'TSH', 'categorie'=>'Hormoni', 'descriere'=>'Analiza pentru evaluarea functiei tiroidiene.', 'pret'=>65, 'pret_vechi'=>65, 'disponibila'=>true, 'promotie'=>false, 'durata'=>15, 'url'=>''),
            array('id'=>204, 'nume'=>'Vitamina D', 'categorie'=>'Vitamine', 'descriere'=>'Determinarea nivelului de vitamina D.', 'pret'=>125, 'pret_vechi'=>125, 'disponibila'=>false, 'promotie'=>false, 'durata'=>20, 'url'=>''),
            array('id'=>205, 'nume'=>'Pachet preventie anuala', 'categorie'=>'Pachete', 'descriere'=>'Set de analize pentru monitorizare generala.', 'pret'=>219, 'pret_vechi'=>275, 'disponibila'=>true, 'promotie'=>true, 'durata'=>30, 'url'=>''),
            array('id'=>206, 'nume'=>'Feritina', 'categorie'=>'Analize uzuale', 'descriere'=>'Indicator al rezervelor de fier din organism.', 'pret'=>79, 'pret_vechi'=>79, 'disponibila'=>false, 'promotie'=>false, 'durata'=>15, 'url'=>''),
            array('id'=>207, 'nume'=>'Pachet echilibru hormonal', 'categorie'=>'Pachete', 'descriere'=>'Analize hormonale grupate intr-un pachet.', 'pret'=>189, 'pret_vechi'=>225, 'disponibila'=>true, 'promotie'=>true, 'durata'=>25, 'url'=>''),
            array('id'=>208, 'nume'=>'Magneziu seric', 'categorie'=>'Vitamine', 'descriere'=>'Masurarea concentratiei serice de magneziu.', 'pret'=>34, 'pret_vechi'=>34, 'disponibila'=>true, 'promotie'=>false, 'durata'=>10, 'url'=>''),
        );
    }
}

if (!function_exists('sante_sim4_wc_products')) {
    function sante_sim4_wc_products() {
        if (!function_exists('wc_get_products')) {
            return array();
        }

        $produse = wc_get_products(array(
            'status'  => 'publish',
            'type'    => 'simple',
            'limit'   => 12,
            'orderby' => 'title',
            'order'   => 'ASC',
        ));
        $rezultat = array();

        foreach ($produse as $produs) {
            $categorii = $produs->get_category_ids();
            $categorie = 'General';
            if (!empty($categorii)) {
                $termen = get_term($categorii[0], 'product_cat');
                if ($termen && !is_wp_error($termen)) {
                    $categorie = $termen->name;
                }
            }

            $pret = (float) wc_get_price_to_display($produs);
            $pret_vechi = $pret;
            if ($produs->is_on_sale() && $produs->get_regular_price() !== '') {
                $pret_vechi = (float) wc_get_price_to_display($produs, array(
                    'price' => (float) $produs->get_regular_price(),
                ));
            }

            $rezultat[] = array(
                'id'          => $produs->get_id(),
                'nume'        => $produs->get_name(),
                'categorie'   => $categorie,
                'descriere'   => $produs->get_short_description(),
                'pret'        => $pret,
                'pret_vechi'  => $pret_vechi,
                'disponibila' => $produs->is_in_stock(),
                'promotie'    => $produs->is_on_sale(),
                'durata'      => 15,
                'url'         => get_permalink($produs->get_id()),
            );
        }
        return $rezultat;
    }
}

if (!function_exists('sante_sim4_load_catalog')) {
    function sante_sim4_load_catalog() {
        // Pentru repetarea testelor, adminul poate utiliza ?sante_demo=1.
        $forteaza_demo = current_user_can('manage_options')
            && isset($_GET['sante_demo'])
            && sanitize_text_field(wp_unslash($_GET['sante_demo'])) === '1';

        $date = $forteaza_demo ? array() : sante_sim4_wc_products();
        $demo = count($date) < 4;
        if ($demo) {
            $date = sante_sim4_demo_data();
        }

        // Ordinea initiala ramane alfabetica. Alte criterii apar in taskurile evaluatorului.
        usort($date, function ($stanga, $dreapta) {
            return strcasecmp($stanga['nume'], $dreapta['nume']);
        });

        return array('analize' => $date, 'demo' => $demo);
    }
}

if (!function_exists('sante_sim4_categories')) {
    function sante_sim4_categories($analize) {
        $categorii = array();
        foreach ($analize as $analiza) {
            $categorii[$analiza['categorie']] = true;
        }
        $nume = array_keys($categorii);
        natcasesort($nume);
        return array_values($nume);
    }
}

if (!function_exists('sante_sim4_format_price')) {
    function sante_sim4_format_price($pret) {
        if (function_exists('wc_price')) {
            return wc_price($pret);
        }
        return number_format_i18n($pret, 2) . ' lei';
    }
}
