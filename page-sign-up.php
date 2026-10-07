<?php

/*
|--------------------------------------------------------------------------
| 0. De retinut
|--------------------------------------------------------------------------
|
| sanitize = CURĂȚ DATELE PRIMITE  ---->  sanitize_email($email);       sanitizare
| escape = PROTEJEZ DATELE CÂND LE AFIȘEZ  ---->  esc_html($message);   escaping la output
| validate = VERIFIC DACĂ SUNT ACCEPTABILE   ---->  is_email($email);   validare
| nonce = VERIFIC REQUEST-UL / ACȚIUNEA   ---->  wp_verify_nonce(...);  verificarea nonce-ului.
|
*/

/*
|--------------------------------------------------------------------------
| 1. NU LĂSĂM USERUL LOGAT SĂ VADĂ SIGN-UP
|--------------------------------------------------------------------------
|
| Dacă userul este deja autentificat, nu mai are sens
| să vadă pagina de creare cont.
| Pe baza sesiunilor se face urmaotoarea verificare
| is_user_logged_in() returnează true / false.
|
*/

if (is_user_logged_in()) {

    // Construim URL-ul către pagina "Contul meu"
    $contul_meu_url = home_url('/contul-meu/');

    // Îl trimitem acolo
    wp_safe_redirect($contul_meu_url);

    // Oprim complet executarea acestui fișier PHP
    exit;  // Nu continuam sa mai rulam ce e mai jos
}


/*
|--------------------------------------------------------------------------
| 2. ÎNCĂRCĂM HEADER-UL TEMEI
|--------------------------------------------------------------------------
*/

get_header();


/*
|--------------------------------------------------------------------------
| 3. VARIABILĂ PENTRU MESAJE
|--------------------------------------------------------------------------
|
| Aici vom pune eventual:
| - Cerere invalidă
| - Email invalid
| - Parolele nu coincid
| - Username deja existent
| etc.
|
*/

$message = '';


/*
|--------------------------------------------------------------------------
| 4. VERIFICĂM DACĂ FORMULARUL A FOST TRIMIS
|--------------------------------------------------------------------------
|
| Când intri prima dată pe pagină, request-ul este GET.
|
| Când apeși butonul "Creează cont", formularul trimite POST.
|
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    /*
    |--------------------------------------------------------------------------
    | 5. VERIFICĂM NONCE-UL
    |--------------------------------------------------------------------------
    |
    | În formular am pus un nonce.
    |
    | Aici verificăm:
    |
    | 1. există acel nonce în $_POST?
    | 2. este valid pentru acțiunea noastră?
    |
    */

    if (
        !isset($_POST['sante_signup_nonce']) || !wp_verify_nonce($_POST['sante_signup_nonce'], 'sante_signup_action')
    ) {

        // Dacă lipsește sau nu este valid, nu procesăm formularul
        $message = 'Cerere invalidă.';

    } else {


        /*
        |--------------------------------------------------------------------------
        | 6. CITIM ȘI SANITIZĂM DATELE PRIMITE
        |--------------------------------------------------------------------------
        |
        | $_POST conține ce a introdus userul în formular.
        |
        | Nu vrem să folosim direct input-ul brut.
        | Îl curățăm cu funcțiile WordPress potrivite.
        |
        */

        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name  = sanitize_text_field($_POST['last_name']);

        // sanitize_user() este făcut special pentru username
        $username   = sanitize_user($_POST['username']);

        // sanitize_email() curăță forma emailului
        $email      = sanitize_email($_POST['email']);

        $phone      = sanitize_text_field($_POST['phone']);


        /*
        |--------------------------------------------------------------------------
        | 7. PAROLELE
        |--------------------------------------------------------------------------
        |
        | Parolele NU le trecem prin sanitize_text_field().
        |
        | Dacă am face asta, am putea modifica parola introdusă
        | de utilizator.
        |
        */

        $password         = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];


        /*
        |--------------------------------------------------------------------------
        | 8. VALIDĂM DATELE
        |--------------------------------------------------------------------------
        |
        | Sanitizare = curățăm valoarea.
        |
        | Validare = verificăm dacă valoarea este acceptabilă.
        |
        */

        if (!is_email($email)) {

            $message = 'Adresa de email nu este validă.';

        } elseif ($password !== $confirm_password) {

            $message = 'Parolele nu coincid.';

        } else {


            /*
            |--------------------------------------------------------------------------
            | 9. CREĂM USERUL
            |--------------------------------------------------------------------------
            |
            | wp_insert_user() este API WordPress.
            |
            | Noi NU scriem manual:
            |
            | INSERT INTO wp_users ...
            |
            | WordPress se ocupă de asta.
            |
            | Tot WordPress face și hashing-ul parolei.
            |
            */

            $user_id = wp_insert_user([

                // user_login, user_email, first_name sunt campurile care exista in up_users din baza de date
                // Introducem in acele campuri variabilele introduse in formularul de log in validate mai sus
                'user_login' => $username,
                'user_pass'  => $password,
                'user_email' => $email,
                'first_name' => $first_name,
                'last_name'  => $last_name,

                // Userul nostru va avea rol simplu
                'role'       => 'subscriber'
            ]);


            /*
            |--------------------------------------------------------------------------
            | 10. VERIFICĂM DACĂ USERUL A FOST CREAT
            |--------------------------------------------------------------------------
            |
            | Dacă merge:
            |
            | $user_id poate fi ceva precum:
            | 4
            | 7
            | 12
            |
            | Dacă apare o problemă:
            | wp_insert_user() întoarce un obiect WP_Error.
            |
            */

            if (is_wp_error($user_id)) {

                // Extragem mesajul din WP_Error
                $message = $user_id->get_error_message();

            } else {


                /*
                |--------------------------------------------------------------------------
                | 11. SALVĂM TELEFONUL ÎN wp_usermeta
                |--------------------------------------------------------------------------
                |
                | wp_users conține datele standard:
                |
                | ID
                | user_login
                | user_pass
                | user_email
                | etc.
                |
                | Telefonul nu este o coloană standard în wp_users.
                |
                | De aceea îl punem în wp_usermeta.
                | Pentru userul cu id-ul 4 ex: Iustin pune in wp_usermeta in campul 'phone' numarul de telefon 074..
                */

                update_user_meta($user_id,'phone', $phone);

                //global $wpbd;
                //$rezultate = $wpbd->get_results("SELECT * FROM {$wpbd->users}"); asta permite sql injection
                //%s = string; %d = integer; %f = float

                /*
                global $wpbd;
                $email = sanitize_email($_POST['email']);
                $sql = $wpbd -> prepare ("SELECT * FROM {$wpdb->users} WHERE user_email = %s", $email);
                $user = $wpdb->get_row($sql);
                */

                /*
                |--------------------------------------------------------------------------
                | 12. REDIRECT DUPĂ CREAREA CONTULUI
                |--------------------------------------------------------------------------
                |
                | Userul a fost creat cu succes.
                |
                | Îl trimitem către Login.
                |
                */

                wp_safe_redirect(home_url('/login/'));


                /*
                |--------------------------------------------------------------------------
                | 13. OPRIM EXECUTAREA
                |--------------------------------------------------------------------------
                |
                | Redirect-ul trimite un header HTTP browserului.
                |
                | Fără exit, PHP ar putea continua să execute
                | codul de mai jos.
                |
                */

                exit;
            }
        }
    }
}

?>

<main class="login-page">

    <div class="signup-box">

        <h1>Creează cont</h1>


        <?php
        /*
        |--------------------------------------------------------------------------
        | 14. AFIȘĂM EVENTUALUL MESAJ DE EROARE
        |--------------------------------------------------------------------------
        */
        ?>

        <?php if ($message): ?>

            <p class="login-message">

                <?php
                /* In acest paragrav ne va afisa de exemplu Parolele nu coincid daca in formular au fost completate gresit
                 * esc_html() se folosește la OUTPUT.
                 *
                 * Chiar dacă $message pare controlat de noi,
                 * este o practică bună să escapăm ce trimitem în HTML.
                 */
                echo esc_html($message); // inseamna Afișează această valoare în HTML, dar fă-o sigură pentru context HTML
                                        // De exemplu, dacă $message ar conține: <script>alert('test')</script> fara esx
                                        //browserul ar putea interpreta acel text ca HTML/JavaScript.
                                        // in forma de mai sus WordPress îl transformă astfel încât să fie afișat ca text, nu executat ca HTML
                ?>

            </p>

        <?php endif; ?> // if ($message): echivalent cu if ($message) '{' si endif; echivalent cu '}'


        <form method="POST">


            <?php
            /*
            |--------------------------------------------------------------------------
            | 15. GENERĂM NONCE-UL
            |--------------------------------------------------------------------------
            |
            | WordPress introduce automat un input hidden.
            |
            | Ceva de forma:
            |
            | <input type="hidden" name="sante_signup_nonce" value="un-token-generat-de-wordpress">
            | 
            |
            | Userul nu vede câmpul.
            |
            | Browserul îl trimite însă odată cu formularul.
            |
            */

            wp_nonce_field('sante_signup_action', 'sante_signup_nonce');
            ?>


            <div class="form-group">

                <label for="first_name">
                    Prenume
                </label>

                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    required
                >

            </div>


            <div class="form-group">

                <label for="last_name">
                    Nume
                </label>

                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    required
                >

            </div>


            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                >

            </div>


            <div class="form-group">

                <label for="phone">
                    Telefon
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Parolă
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >

            </div>


            <div class="form-group">

                <label for="confirm_password">
                    Confirmă parola
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    required
                >

            </div>


            <button type="submit">
                Creează cont
            </button>

        </form>


        <p class="signup-link">

            Ai deja cont?

            <a href="<?php echo esc_url(home_url('/login/')); ?>">
                Autentifică-te
            </a>

        </p>

    </div>

</main>

<?php get_footer(); ?>