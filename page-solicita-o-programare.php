<?php
get_header();
?>

<?php 
$erori = array();
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_POST['programare_nonce']) || !wp_verify_nonce($_POST['programare_nonce'], 'programare_action')) {

        $erori[] = 'Verificarea de securitate a esuat.';

    } else {

        $nume = sanitize_text_field($_POST['nume']);

        $telefon = sanitize_text_field($_POST['telefon']);

        $specialitate = sanitize_text_field($_POST['specialitate']);

        $email = sanitize_email($_POST['email']);

        $mesaj = sanitize_text_field($_POST['mesaj']);

        if (empty($nume)) {
            $erori[] = 'Numele este obligatoriu.';
        }
        
        if (empty($email)) {
            $erori[] = 'Emailul este obligatoriu.';
        }

        if (empty($telefon)) {
            $erori[] = 'Telefonul este obligatoriu.';
        }

        if (!empty($email) && !is_email($email)) {
            $erori[] = 'Emailul nu este valid.';
        }

        if (empty($erori)) {
            $succes = true;
        }

    }

}

?>

<div class="container py-5">

    <h1 class="zona-informatii">Solicita o programare</h1>

    <?php
    if (!empty($erori)) {

        echo '<div class="alert alert-danger mesaje-formular">';

        foreach ($erori as $eroare) {

            echo '<p class="mb-1">' . esc_html($eroare) . '</p>';

        }

        echo '</div>';

    }
    ?>

    <?php
    if ($succes) {

        echo '<div class="alert alert-success mesaje-formular">
                Solicitarea a fost trimisă cu succes.
            </div>';

    }
    ?>

    <?php
    if ($succes) {

        echo '<div class="alert alert-success">
                Solicitarea a fost trimisă cu succes.
            </div>';

    }
    ?>

<form method="POST" class="formular-programare">

    <?php wp_nonce_field('programare_action','programare_nonce'); ?>

    <div class="mb-3">

        <label for="nume" class="form-label">Nume</label>
        <input type="text" class="form-control" id="nume" name="nume">

    </div>


    <div class="mb-3">

        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email">

    </div>


    <div class="mb-3">

        <label for="telefon" class="form-label">Telefon</label>
        <input type="text" class="form-control" id="telefon" name="telefon">

    </div>

    <div class="mb-3">

        <label for="specialitate" class="form-label">Specialitate</label>

        <select id="specialitate" name="specialitate" class="form-select">

        <option value="">Alege specialitatea</option>
        <option value="cardiologie">Cardiologie</option>
        <option value="dermatologie">Dermatologie</option>
        <option value="medicină-internă">Medicină internă</option>

        </select>

    </div>

    <div class="mb-3">

    <label for="mesaj" class="form-label">Mesaj</label>

    <textarea id="mesaj" name="mesaj" class="form-control" rows="4"></textarea>

    <button type="submit" class="btn btn-primary">Trimite</button>

    </div>

</form>

</form>

    <div class="zona-informatii">

        <button type="button" id="informatii-programare" class="btn btn-primary mt-4">
            Vezi informatii despre programare</button>

        <p id="mesaj-programare" class="mt-3" style="display: none;">
            Un operator vă va contacta pentru confirmarea datei și orei.
        </p>

    </div>

</div>

<?php
get_footer();
?>