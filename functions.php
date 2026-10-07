<?php

/**
 * @package Bootscore Child
 *
 * @version 6.0.0
 */


// Exit if accessed directly
defined('ABSPATH') || exit;


/**
 * Enqueue scripts and styles
 */
add_action('wp_enqueue_scripts', 'bootscore_child_enqueue_styles');
function bootscore_child_enqueue_styles() {

  // Compiled main.css
  $modified_bootscoreChildCss = date('YmdHi', filemtime(get_stylesheet_directory() . '/assets/css/main.css'));
  wp_enqueue_style('main', get_stylesheet_directory_uri() . '/assets/css/main.css', array('parent-style'), $modified_bootscoreChildCss);

  // style.css
  wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
  
  // custom.js
  // Get modification time. Enqueue file with modification date to prevent browser from loading cached scripts when file content changes. 
  $modificated_CustomJS = date('YmdHi', filemtime(get_stylesheet_directory() . '/assets/js/custom.js'));
  wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), $modificated_CustomJS, false, true);

 /* 
 WordPress creează pentru JavaScript un obiect aproximativ:
santeAjax = {
    ajax_url: "http://myapp.test/wp-admin/admin-ajax.php",
    nonce: "abc123..."
}
    Practic PHP ii paseaza niste date unui JS
 */
 
/*
|--------------------------------------------------------------------------
| Date pentru AJAX-ul clasic WordPress
|--------------------------------------------------------------------------
*/

wp_localize_script(
    'custom-js',
    'santeAjax',
    [
        // URL către admin-ajax.php
        'ajax_url' => admin_url('admin-ajax.php'),

        // Nonce-ul pentru acțiunea AJAX
        'nonce' => wp_create_nonce('sante_numar_useri_action')
    ]
);


/*
|--------------------------------------------------------------------------
| Date pentru REST API
|--------------------------------------------------------------------------
*/

wp_localize_script(
    'custom-js',
    'santeRest',
    [
        // Endpoint-ul REST complet
        'url' => rest_url('sante/v1/email-meu'),

        // Nonce special pentru REST API
        'nonce' => wp_create_nonce('wp_rest')
    ]
);

}





// Creem endpoint-ul: /wp-json/sante/v1/email-meu si spune:
// dacă vine un GET aici, execută sante_rest_email_meu()
add_action('rest_api_init', function () {

    register_rest_route(
        'sante/v1',
        '/email-meu',
        [
            // Endpoint-ul răspunde la GET
            'methods' => 'GET',

            // Funcția PHP care va returna emailul
            'callback' => 'sante_rest_email_meu',

            // Permitem acces doar userilor autentificați
            'permission_callback' => function () {
                return is_user_logged_in();
            }
        ]
    );

});

// Nu avem nevoie de $wpdb aici, pentru că WordPress deja știe userul curent și ne oferă: wp_get_current_user()
function sante_rest_email_meu() {

    // Luăm userul autentificat
    $user = wp_get_current_user();

    /*
     * Returnăm un array PHP.
     * WordPress îl transformă automat în JSON.
    */
    return ['user_email' => $user->user_email];
}

function modifica_titlul($titlu, $post_id) {

    if (is_page('login')) {
        return 'Zona personală';
    }

    return $titlu;
}

add_filter('the_title', 'modifica_titlul', 10, 2); // Daca reuseste autentificarea modifica titlul in Zona personală

function sante_dupa_inregistrare($user_id) {

    update_user_meta(
        $user_id,
        'registration_source',
        'formular_sante'
    );
}

add_action('user_register', 'sante_dupa_inregistrare'); // WordPress are un action hook numit: user_register se declanșează după ce un utilizator este creat.


// __________________________________
// MAI JOS FACEM PARTEA DE AJAX
// Primesc date din custom.js cu date.append('action', 'sante_numar_useri');
// __________________________________

function sante_numar_useri() {

    // 1. // Verificăm nonce-ul primit din JavaScript (custom.js) date.append('nonce', santeAjax.nonce);
    // Dacă nonce-ul nu este valid, WordPress oprește request-ul (cerere).
    // sante_numar_useri_action este numele acțiunii pentru care generăm nonce-ul
    // nonce este numele câmpului din cerere unde JavaScript va trimite token-ul
    // JavaScript trebuie să trimită ceva de forma: nonce = tokenul_generat_de_wordpress
    check_ajax_referer('sante_numar_useri_action', 'nonce');


    // 2. Verificăm dacă userul are voie să facă acțiunea.
    // Pentru exemplu cerem dreptul de a edita articole.
    // Dacă userul NU are dreptul edit_posts, oprește acțiunea.
    if (!current_user_can('edit_posts')) {

        wp_send_json_error(['message' => 'Nu ai permisiunea necesară.']);
    }


    // 3. Luăm acces la obiectul WordPress pentru baza de date.
    global $wpdb;


    // 4. Numărăm userii creați prin formularul nostru.
    // Dca in baza de date usermeta exista 2 campuri de exemplu care corespund unde pe coloana meta_key avem 
    // registration_source si pe coloana meta_value avem formular_sante (registration_source = formular_sante)
    // numara de cate ori se intampla asta
    $numar_useri = $wpdb->get_var( // folsoim get_var() pentru ca ne va returna o singura valoare
                   $wpdb->prepare(
                    "SELECT COUNT(*)
                    FROM {$wpdb->usermeta}
                    WHERE meta_key = %s
                    AND meta_value = %s",
                    'registration_source',
                    'formular_sante'
        )
    );


    // 5. Trimitem răspuns JSON către JavaScript. in custom.js
    wp_send_json_success(['numar_useri' => (int) $numar_useri]); // numar_useri primeste val 2
    // WordPress va răspunde aproximativ cu:
    /* 
      {
        "success": true,
        "data": {
            "numar_useri": 2
        }
      }

    Nu trebuie sa construim noi JSON-UL manual
    Daca userul nu are permisiuni functia wp_send_json_error() va trimite in JSON

      {
        "success": false,
        "data": {
            "message": "Nu ai permisiunea necesară."
        }
      }

    */
}


add_action('wp_ajax_sante_numar_useri', 'sante_numar_useri');



// Intervenim peste validarea standard făcută de Contact Form 7
add_filter(
    'wpcf7_validate_text*',
    'sante_valideaza_subiect',
    10,
    2
);


/* 

Pentru mai jos logica e:

Form
→ ce completează userul

Mail
→ unde și cum se trimite

wpcf7_* hooks
→ unde intervenim noi cu logică custom

*/

function sante_valideaza_subiect($result, $tag) {

    /*
     * Verificăm dacă field-ul validat este exact câmpul nostru "your-subject".
     */
    if ($tag->name === 'your-subject') {

        /*
         * Luăm valoarea trimisă din formular.
         */
        $subject = isset($_POST['your-subject'])
            ? sanitize_text_field($_POST['your-subject'])
            : '';

        /*
         * Dacă are mai puțin de 5 caractere,
         * marcăm câmpul ca invalid.
         */
        if (mb_strlen($subject) < 5) {

            $result->invalidate(
                $tag,
                'Subiectul trebuie să aibă minimum 5 caractere.'
            );
        }
    }

    /*
     * Foarte important:
     * fiind filter, trebuie să returnăm rezultatul.
     */
    return $result;
}


add_action('wpcf7_before_send_mail', 'sante_ruteaza_email', 10, 3);

function sante_ruteaza_email($contact_form, &$abort, $submission) {

    // Luăm valoarea aleasă în formular din array
    $departament = $submission->get_posted_data('departament');

    if (is_array($departament)) {
        $departament = reset($departament);
    }

    // Luăm configurația emailului
    $mail = $contact_form->prop('mail');

    // Alegem destinatarul în funcție de departament
    if ($departament === 'Programari') {

        $mail['recipient'] = 'programari@example.com';

    } elseif ($departament === 'Rezultate') {

        $mail['recipient'] = 'rezultate@example.com';
    }

    error_log('Departament ales: ' . $departament);
    error_log('Recipient final: ' . $mail['recipient']);

    // Punem configurația modificată înapoi în formular
    $contact_form->set_properties([
        'mail' => $mail
    ]);
}

add_action(
    'woocommerce_order_status_changed',
    'sante_order_status_changed',
    10,
    4
);

function sante_order_status_changed($order_id, $old_status, $new_status, $order) {

    // Scriem în debug.log ID-ul comenzii și schimbarea de status.
    error_log(
        'Comanda ' . $order_id .
        ' s-a schimbat din ' . $old_status .
        ' in ' . $new_status
    );
}

add_action(
    'woocommerce_store_api_checkout_update_order_from_request',
    'sante_adauga_sursa_comanda',
    10,
    2
);

function sante_adauga_sursa_comanda($order, $request) {

    // Adăugăm metadata pe comandă le gasesc in wp_wc_orders_meta
    $order->update_meta_data(
        'sursa_comanda',
        'checkout_sante'
    );

    error_log(
        'STORE API checkout pentru comanda: ' . $order->get_id()
    );
}