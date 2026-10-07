document.addEventListener('DOMContentLoaded', function() {

    /*
    |--------------------------------------------------------------------------
    | BUTON 1: Verifică utilizatorii
    |--------------------------------------------------------------------------
    |
    | Acest buton folosește AJAX-ul clasic WordPress:
    |
    | JavaScript
    |   ↓
    | admin-ajax.php
    |   ↓
    | action = sante_numar_useri
    |   ↓
    | wp_ajax_sante_numar_useri
    |   ↓
    | callback PHP
    |
    */

    const butonUseri = document.getElementById('verifica-useri');
    const rezultatUseri = document.getElementById('rezultat-useri');


    // Verificăm dacă butonul există pe pagina curentă.
    // Dacă nu există, pur și simplu sărim peste această funcționalitate.
    if (butonUseri) {

        // Ascultăm click-ul pe buton.
        butonUseri.addEventListener('click', function() {

            /*
             * FormData este obiectul în care punem datele
             * pe care vrem să le trimitem către PHP.
             */
            const date = new FormData();


            /*
             * "action" este foarte important în AJAX-ul WordPress.
             *
             * Trimitem:
             *
             * action = sante_numar_useri
             *
             * WordPress va transforma asta în hook-ul:
             *
             * wp_ajax_sante_numar_useri
             */
            date.append(
                'action',
                'sante_numar_useri'
            );


            /*
             * Trimitem și nonce-ul AJAX.
             *
             * Acest nonce a fost generat în PHP cu:
             *
             * wp_create_nonce('sante_numar_useri_action')
             */
            date.append(
                'nonce',
                santeAjax.nonce
            );


            /*
             * Trimitem request-ul către:
             *
             * /wp-admin/admin-ajax.php
             *
             * URL-ul este primit din PHP prin wp_localize_script().
             */
            fetch(
                santeAjax.ajax_url, {
                    method: 'POST',
                    body: date
                }
            )

            /*
             * Serverul ne trimite JSON.
             *
             * response.json() îl transformă într-un obiect JavaScript.
             */
            .then(response => response.json())

            .then(data => {

                /*
                 * Dacă PHP a folosit:
                 *
                 * wp_send_json_success(...)
                 *
                 * atunci:
                 *
                 * data.success === true
                 */
                if (data.success) {

                    /*
                     * PHP a trimis:
                     *
                     * {
                     *   success: true,
                     *   data: {
                     *      numar_useri: 2
                     *   }
                     * }
                     *
                     * De aceea accesăm:
                     *
                     * data.data.numar_useri
                     */
                    rezultatUseri.textContent =
                        'Există ' +
                        data.data.numar_useri +
                        ' utilizatori creați prin formular.';

                } else {

                    /*
                     * Dacă PHP a folosit:
                     *
                     * wp_send_json_error(...)
                     */
                    rezultatUseri.textContent =
                        data.data.message;
                }

            })

            /*
             * Aici ajungem dacă apare o eroare tehnică,
             * de exemplu request-ul nu poate fi făcut.
             */
            .catch(error => {

                rezultatUseri.textContent =
                    'A apărut o eroare la request.';

                console.error(error);
            });

        });
    }


    /*
    |--------------------------------------------------------------------------
    | BUTON 2: Arată emailul meu
    |--------------------------------------------------------------------------
    |
    | Acest buton NU folosește admin-ajax.php.
    |
    | Folosește REST API:
    |
    | JavaScript
    |   ↓
    | GET /wp-json/sante/v1/email-meu
    |   ↓
    | register_rest_route()
    |   ↓
    | permission_callback
    |   ↓
    | callback PHP
    |   ↓
    | JSON
    |
    */

    const butonEmail = document.getElementById('arata-email');
    const rezultatEmail = document.getElementById('rezultat-email');


    // Dacă butonul există, îi atașăm evenimentul.
    if (butonEmail) {

        butonEmail.addEventListener('click', function() {

            /*
             * Trimitem GET către endpoint-ul REST.
             *
             * santeRest.url este ceva de forma:
             *
             * http://myapp.test/wp-json/sante/v1/email-meu
             */
            fetch(
                santeRest.url, {
                    method: 'GET',

                    /*
                     * Trimitem nonce-ul REST în header.
                     *
                     * WordPress folosește acest nonce
                     * pentru a recunoaște request-ul autentificat.
                     */
                    headers: {
                        'X-WP-Nonce': santeRest.nonce
                    }
                }
            )

            // Transformăm răspunsul JSON în obiect JavaScript.
            .then(response => response.json())

            .then(data => {

                /*
                 * Endpoint-ul nostru returnează ceva de forma:
                 *
                 * {
                 *   "user_email": "dan@gmail.com"
                 * }
                 *
                 * De aceea aici accesăm:
                 *
                 * data.user_email
                 */
                if (data.user_email) {

                    rezultatEmail.textContent =
                        'Emailul tău este: ' +
                        data.user_email;

                } else {

                    /*
                     * Dacă nu primim user_email,
                     * afișăm un mesaj generic.
                     *
                     * console.log(data) ne ajută să vedem
                     * ce a trimis serverul de fapt.
                     */
                    rezultatEmail.textContent =
                        'Nu am putut obține emailul.';

                    console.log(data);
                }

            })

            .catch(error => {

                rezultatEmail.textContent =
                    'A apărut o eroare la request.';

                console.error(error);
            });

        });
    }

});



/*

document.addEventListener('DOMContentLoaded', function() {

    // Căutăm elementele în HTML
    const titlu = document.getElementById('titlu-rezultate');
    const descriere = document.getElementById('descriere-rezultate');

    // Dacă nu suntem pe o pagină unde există aceste elemente,
    // nu continuăm.
    if (!titlu || !descriere) {
        return;
    }

    // Ascultăm click-ul pe titlu
    titlu.addEventListener('click', function() {

        // Dacă descrierea este ascunsă...
        if (descriere.style.display === 'none') {

            // ...o afișăm
            descriere.style.display = 'block';

        } else {

            // altfel o ascundem
            descriere.style.display = 'none';
        }
    });

});
tot ce am scris mai sus (DOM manipulation: JavaScript) face ca atunci cand dam click pe pe rezultate online sa imi dispare descrierea
Cand dau iar apare descrierea dar putem face asta mult mai rapid folosind jQuery (uite codul mai jos)
*/

//DOM-ul este modul în care browserul transformă HTML-ul paginii într-o 
//DOM-ul nu este exact același lucru cu fișierul HTML. HTML-ul este codul inițial, iar DOM-ul este reprezentarea 
//„vie” a paginii în browser, care poate fi modificată cu JavaScript fără să reîncarci pagina.
//structură de obiecte pe care JavaScript o poate citi și modifica.
//jQuery(document).ready(...) așteaptă să fie încărcat DOM-ul (Document Object Model) iar $ = alias pentru jQuery

/*

SELECTORI de baza:

$('#id')          // element după id
$('.clasa')       // toate elementele cu o clasă
$('p')            // toate tagurile <p>
$('.card h3')     // toate h3 din .card

*/

/*

EXPLICATII

1) JavaScript vanilla este limbajul de bază din browser. Cu el poți modifica DOM-ul, 
asculta click-uri, face request-uri HTTP etc.

ex:
const titlu = document.getElementById('titlu-rezultate');

titlu.addEventListener('click', function() {
    // ceva
});

2)jQuery este o bibliotecă scrisă peste JavaScript. Nu înlocuiește JavaScript-ul; 
doar îți dă metode mai scurte și mai comode.

ex: (scriem ce avem mai sus mai simplu)
$('#titlu-rezultate').on('click', function() {
    // ceva
});

3)fetch() este altceva. Este o funcție JavaScript folosită pentru a trimite request-uri HTTP către server.

ex: 
fetch('/wp-json/sante/v1/email-meu')

e la stilul: Browser, du-te la URL-ul ăsta, cere date și adu-mi răspunsul.
Deci fetch() nu e pentru DOM în primul rând. E pentru comunicarea cu serverul.

4)AJAX este conceptul mai larg: trimiți/primești date de la server fără să reîncarci toată pagina.
Aici e confuzia frecventă:
AJAX = ideea / tehnica
fetch() = una dintre metodele prin care poți face AJAX
jQuery.ajax() = altă metodă prin care poți face AJAX

am făcut un request AJAX folosind fetch().
Iar serverul a primit request-ul prin: admin-ajax.php și WordPress a decis ce PHP să execute din:
add_action('wp_ajax_sante_numar_useri', 'sante_numar_useri');

REZUMAT:
JavaScript  ->	limbajul
jQuery  ->	bibliotecă JavaScript
fetch()  ->	funcție JS pentru request-uri HTTP
AJAX  ->   tehnica de comunicare fără refresh
REST API  ->  mod organizat de a expune endpoint-uri HTTP

jQuery sau JavaScript?, răspunsul bun este: 
jQuery este doar o abstracție peste JavaScript; 
azi multe lucruri se fac direct în vanilla JS, dar în WordPress
îl întâlnești foarte des în teme și pluginuri existente.

*/

jQuery(document).ready(function($) {

    // =========================
    // FORMULAR SALUT
    // =========================

    $('#formular-salut').on('submit', function(event) {

        // Oprim submit-ul clasic + refresh-ul paginii
        event.preventDefault();

        // Luăm valoarea introdusă în input
        const nume = $('#nume-test').val();

        // Dacă inputul este gol
        if (nume === '') {
            $('#mesaj-salut').text('Introdu un nume.');
            return;
        }

        // Modificăm textul din pagină
        $('#mesaj-salut').text(
            'Salut, ' + nume + '!'
        );
    });


    // =========================
    // SELECT DEPARTAMENT
    // =========================

    $('#departament-test').on('change', function() {

        // this = select-ul care și-a schimbat valoarea
        const valoare = $(this).val();

        $('#departament-ales').text(
            'Ai ales: ' + valoare
        );
    });


    // =========================
    // CARDURI BENEFICII
    // =========================

    $('.btn-detalii').on('click', function() {

        // Butonul apăsat
        const buton = $(this);

        // Găsim cardul părinte
        const card = buton.closest('.beneficiu-card');

        // Găsim descrierea doar din acel card
        const descriere = card.find('.descriere-beneficiu');

        // Ascundem / afișăm descrierea
        descriere.toggle();
    });

});