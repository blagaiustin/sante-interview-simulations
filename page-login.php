<?php

// Dacă userul este deja autentificat,
// nu mai are sens să vadă pagina de login.
if (is_user_logged_in()) {

    wp_safe_redirect(home_url('/contul-meu/'));

    exit;
}

get_header();

?>

<?php

get_header();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = sanitize_text_field($_POST['email']); //ne protejam de codul dubios HTML care poate fi introdus
    $password = $_POST['password'];

    // wp_signon() -> WordPress cauta userul, verifica parola hash-uita si daca e corecta creaza cookie de autentificare
    // Daca merge: variabila $user devine un obiect: WP_User ce contine date de autentificare
    $user = wp_signon([ //citeste din baza de date si verifica. Nu mai dam noi comenzi SQL. Se ocupa API-aiul de la WordPress
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => true
    ]); //daca merge Log in-ul variabila $user va avea WP_User salvad si daca nu va avea WP_Error

    if (is_wp_error($user)) {

        $message = 'Username sau parolă greșită.';

    } else {

        wp_safe_redirect(home_url('/contul-meu/'));
        exit; //Redirectul trimite headerul HTTP către browser, dar PHP ar putea continua să execute restul fișierului. Noi spunem: exit ca sa se opreasca
    }
}

?>

<main class="login-page">

    <div class="login-box">

        <h1>Autentificare</h1>

        <?php if ($message): ?>
            <p class="login-message">
                <?php echo esc_html($message); ?>
            </p>
        <?php endif; ?>

        <form class="login-form" method="POST">

            <div class="form-group">
                <label for="email">Email</label>
                
                <input
                    type="text"
                    id="email"
                    name="email"
                    placeholder="exempluemail@email.com"
                >

            </div>

            <div class="form-group">
                <label for="password">Parolă</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Introdu parola"
                >

            </div>

            <button type="submit">
                Login
            </button>

            <p class="signup-link">
                Nu ai cont?
                <a href="<?php echo esc_url( home_url('/sign-up/') ); ?>">
                    Creează unul
                </a>
            </p>

        </form>

    </div>

</main>

<?php

get_footer();