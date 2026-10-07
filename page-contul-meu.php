<?php

// Dacă userul NU este autentificat,
// nu îi permitem accesul la pagina Contul meu.
if (!is_user_logged_in()) {

    wp_safe_redirect(home_url('/login/'));

    exit;
}

get_header();


// Verificăm dacă WordPress are rezultate pentru pagina curentă
if (have_posts()) {

    // Parcurgem rezultatele query-ului principal
    while (have_posts()) {

        // Setează postarea/pagina curentă în contextul WordPress
        the_post();

        ?>

        <main class="container py-5">

            <!-- Afișează titlul paginii "Contul meu" -->
            <h1><?php the_title(); ?></h1>

            <!-- Afișează conținutul scris în editorul WordPress -->
            <?php the_content(); ?>
            
            <?php

            // Citim valoarea câmpului ACF cu field name = mesaj_personal
            $mesaj = get_field('mesaj_personal');

            // Dacă există o valoare, o afișăm
            if ($mesaj) {
                echo '<p>' . esc_html($mesaj) . '</p>';
            }

            ?>

            <?php

            // Luăm valorile din ACF
            $beneficiu_1_titlu = get_field('beneficiu_1_titlu');
            $beneficiu_1_descriere = get_field('beneficiu_1_descriere');

            $beneficiu_2_titlu = get_field('beneficiu_2_titlu');
            $beneficiu_2_descriere = get_field('beneficiu_2_descriere');

            ?>
            <!-- 
            Bootstrap utilities le folosești când vrei rapid spațiere, 
            aliniere, grid, display, shadow, culori standard etc.
            CSS/SCSS custom îl folosești când designul tău nu mai încape 
            elegant în clasele Bootstrap sau vrei o identitate vizuală proprie. 
 
            100% latime pe mobil si 50% lățime de la md în sus
                    sm = small; md = medie; lg =large; xl; xxl
                    md pragul e de 768 px latime
                    adica sub 768 px latime pune: 
                    [card 1    ]
                    [card 2    ]
                    iar peste 768 px latime pune:
                    [ Card 1 ][ Card 2 ]
                    12 = 100% si 6 = 50%
                    md = când?
                    6  = cât?
                    ex: cal-12 col-lg-4 inseamna pe ecrane mici și medii: 100% de lg in sus 4/12 = 33%
                    row = toată lățimea disponibilă
                    |1|2|3|4|5|6|7|8|9|10|11|12|   col-lg-4 o sa avem:
                    [----4 coloane----][----4 coloane----][----4 coloane----]
                    [      CARD 1     ][      CARD 2     ][      CARD 3     ]
                    row = 12 coloane
                    col-12 = 12/12 = 100%
                    col-6  = 6/12  = 50%
                    col-4  = 4/12  = 33.3%
                    col-3  = 3/12  = 25%
                    <div class="col-12 col-md-6 col-lg-4"> vine asa:
                    telefon:
                    [ CARD 1 ]
                    [ CARD 2 ]
                    [ CARD 3 ]

                    tabletă:
                    [ CARD 1 ][ CARD 2 ]
                    [ CARD 3 ]

                    desktop:
                    [ CARD 1 ][ CARD 2 ][ CARD 3 ]
                    card h-100 p-3   inseamna:
                    card = stil de card Bootstrap 
                    h-100 = înălțime 100% față de containerul disponibil
                    p-3 = padding în interior
                    my-4 = margin top + bottom
                    g-3 = gap între coloanele din row

                    clasele astea sunt doar prescurtări pentru CSS. De exemplu:
                    p-4       → padding
                    mb-2      → margin-bottom
                    mb-0      → margin-bottom: 0
                    shadow-sm → box-shadow    
                    Următorul pas bun e să vedem când merită să 
                    folosim clase Bootstrap și când e mai bine să scriem noi CSS/SCSS custom
                    -->

            <div class="container my-4">
                <div class="row g-3">

                    <?php if ($beneficiu_1_titlu): ?>

                        <div class="col-12 col-md-6">

                            <div class="card beneficiu-card h-100 p-4 shadow-sm">

                                <h3 class="mb-2">
                                    <?php echo esc_html($beneficiu_1_titlu); ?>
                                </h3>

                                <?php if ($beneficiu_1_descriere): ?>

                                    <p class="descriere-beneficiu mb-3">
                                        <?php echo esc_html($beneficiu_1_descriere); ?>
                                    </p>

                                <?php endif; ?>

                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-detalii"
                                >
                                    Vezi mai multe detalii
                                </button>

                            </div>

                        </div>

                    <?php endif; ?>


                    <?php if ($beneficiu_2_titlu): ?>

                        <div class="col-12 col-md-6">

                            <div class="card beneficiu-card h-100 p-4 shadow-sm">

                                <h3 class="mb-2">
                                    <?php echo esc_html($beneficiu_2_titlu); ?>
                                </h3>

                                <?php if ($beneficiu_2_descriere): ?>

                                    <p class="descriere-beneficiu mb-3">
                                        <?php echo esc_html($beneficiu_2_descriere); ?>
                                    </p>

                                <?php endif; ?>

                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-detalii"
                                >
                                    Detalii programari
                                </button>

                            </div>

                        </div>

                        <form id="formular-salut" class="mt-4">
                        <label for="nume-test" class="form-label">
                            Numele tău
                        </label>

                        <input
                            type="text"
                            id="nume-test"
                            class="form-control"
                        >

                        <button
                            type="submit"
                            class="btn btn-primary mt-2"
                        >
                            Salută-mă
                        </button>
                    </form>

                    <p id="mesaj-salut" class="mt-3"></p>

                    <select id="departament-test" class="form-select mt-3">
                        <option value="">Alege departamentul</option>
                        <option value="Programari">Programări</option>
                        <option value="Rezultate">Rezultate</option>
                    </select>

                    <p id="departament-ales" class="mt-2"></p>

                    <?php endif; ?>

                </div>
            </div>

            <?php

            $imagine = get_field('imagine_profil');

            /* 
             $imagine = [
                'url'    => 'http://myapp.test/wp-content/uploads/poza.jpg',
                'alt'    => 'Poza profil',
                'width'  => 1200,
                'height' => 800
            ];
            de aia putem scrie ($imagine['url'] ca sa luam url-ul
            */

            if ($imagine) {

                // Folosim doua functii: esc_url si esc_attr
                // esc_url e pentru ...
                // esc_attr e pentru ...
                echo '<img 
                    src="' . esc_url($imagine['url']) . '" 
                    alt="' . esc_attr($imagine['alt']) . '"
                    width="200"
                >';
            }

            ?>

            <?php

            // Luăm articolele selectate din field-ul ACF Relationship
            $articole_recomandate = get_field('articole_recomandate');

            // Verificăm dacă avem cel puțin un articol selectat
            if ($articole_recomandate) {

                echo '<h2>Articole recomandate</h2>';

                // Parcurgem fiecare articol selectat
                foreach ($articole_recomandate as $articol) {

                    echo '<div class="articol-recomandat">';

                    // Afișăm titlul articolului
                    echo '<h3>' .
                        esc_html(get_the_title($articol)) .
                        '</h3>';

                    // Afișăm linkul către articol
                    echo '<a href="' .
                        esc_url(get_permalink($articol)) .
                        '">Citește articolul</a>';

                    echo '</div>';
                }
            }

            ?>

            <?php
                global $wpdb; // O folosesc ca sa pot insera instructiuni manuale sql pentru interactiunea cu baza de date
                             //  In cazul asta nu era necesar neaparat dar le-am folosit pentru a intelege cum se poate face 

                // Obținem obiectul utilizatorului autentificat
                $user = wp_get_current_user(); // Acolo avem si first_name, phone, session_tokens (le gasesc in wp_usermeta la meta_key in baza de date)
                                               // Acum le avem pe toate in obiectul user 
               
                $sql = $wpdb->prepare (
                    "SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key = %s", $user->ID, 'registration_source'
                    ); // la coloana meta_value din wp_usermeta unde id-ul userului = 5 si meta_key = 'registration_source' preia valoarea adica formular_sante
                    
                // get_var folosim cand vrem o singura valoare
                // Daca vrem un singur rand folosim get_row ex: SELECT ID, user_login, user_email FROM wp_users WHERE ID = 4
                // Pentru mai multe randuri folosim get_results si apoi parcurgem fu foreach ($rezultate as $rand) 
                $rezultat = $wpdb->get_var($sql);  //salveaza in cariabila asta valoarea formular_sante

                $sql2 = $wpdb->prepare (
                    "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s", 'registration_source', 'formular_sante'
                    );

                $rezultate = $wpdb->get_results($sql2);

                // Putem face si un JOIN wp_users are: ID | user_login | user_email si wp_usermeta are user_id | meta_key | meta_value
                // Un JOIN spune practic: Unește rândurile din cele două tabele unde ID-urile corespund.
                // $sql2 = $wpdb->prepare (
                // "SELECT u.ID, u.user_login, u.user_email 
                //  FROM {$wpdb->users} AS um ON u.ID = um.user_id WHERE um.meta_key = %s AND um.meta_value = %s",'registration_source', 'formular_sante'
               //   );
                

                // Afișăm prenumele utilizatorului
                // esc_html() protejează output-ul înainte de afișare
                echo '<h2>Salut, ' .
                    esc_html($user->first_name) .
                    '!</h2>';

                //facem diferenta intre conturile standard si conturile de admin
                if (current_user_can('edit_posts')) {

                    echo '<p>Ai dreptul să editezi articole.</p>';

                } else {

                    echo '<p>Nu ai dreptul să editezi articole.</p>';
                }

                echo '<h2>In registration_source scrie: ' .
                    esc_html($rezultat) .  // afiseaza in pagina contul-meu intr-un h2 In registration_source scrie: formular_sante
                    '!</h2>';

                    echo '<h2>Avem in total: ' .count($rezultate).  ' useri creati prin formularul nou. Se adacuga cate un formular_sante in dreptul fiecarui registration_source din meta_key pentru fiecare user nou creat. Asta ne ajuta sa ii gasim</h2>';

                foreach ($rezultate as $rand1) {

                    $user_data = get_userdata($rand1->user_id);

                    echo '<h3>user_login: ' .
                        esc_html($user_data->user_login) .
                        '</h3>';

                    echo '<h3>ID: ' .
                        esc_html($user_data->ID) .
                        '</h3>';


                    // Pentru a putea afisa si Sursă: formular_sante trebuie sa facem un query diferit
                    // Daca as face un JOIN am putea salva totul intr-un singur query si l-as putea afisa fara codul de jos
                    $sursa = get_user_meta($user_data->ID, 'registration_source', true); 

                    echo '<h3>Sursă: ' . esc_html($sursa) . '</h3>';
                }

                ?>



                <a href="<?php echo esc_url(wp_logout_url(home_url('/login/'))); ?>">Logout
                </a>

                <hr>

                <button type="button" id="verifica-useri" class="btn btn-primary">
                Verifică utilizatorii
                </button>

                <!-- Va fi scris in acest p sub butonul Verifică utilizatorii: Există 2 utilizatori creați prin formular.  
                daca lucrurile functioneaza -->
                <p id="rezultat-useri"></p>

                <button type="button" id="arata-email" class="btn btn-secondary">
                Arată emailul meu
                </button>

                <!-- afiseaza in acest p emailul userului conectat daca apasam pe butonul de mai sus: Arată emailul meu -->
                <p id="rezultat-email"></p>

                <h2>Ultimele articole</h2>

                <?php

                /*
                 * Creăm un query NOU, separat de query-ul principal.
                 *
                 * Query-ul principal este cel care a găsit pagina "Contul meu".
                 * Acest query secundar caută articole.
                 */
                $articole = new WP_Query([

                    // Vrem doar postări/articole
                    'post_type' => 'post',

                    // Afișăm maximum 3 articole
                    'posts_per_page' => 2,

                    'author_name' => 'IustinBlaga',

                    // Le ordonăm după data publicării
                    'orderby' => 'title',

                    // DESC = cele mai noi primele
                    'order' => 'ASC'

                ]);


                // Verificăm dacă query-ul nostru a găsit articole
                if ($articole->have_posts()) {

                    // Parcurgem articolele găsite
                    while ($articole->have_posts()) {

                        /*
                         * IMPORTANT:
                         * folosim $articole->the_post(),
                         * nu simplu the_post(),
                         * pentru că lucrăm cu query-ul nostru secundar.
                         */
                        $articole->the_post();

                        ?>

                        <article class="mb-4">

                            <h3>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <?php the_excerpt(); ?>

                        </article>

                        <?php
                    }

                } else {

                    echo '<p>Nu există articole.</p>';
                }


                /*
                 * După ce terminăm WP_Query-ul secundar,
                 * readucem WordPress la contextul query-ului principal.
                 */
                wp_reset_postdata();

            ?>

        </main>

    <?php
    }
}


// Încarcă footer-ul temei
get_footer();