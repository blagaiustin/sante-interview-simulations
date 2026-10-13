// Codul se adauga la SFARSITUL fisierului existent assets/js/custom.js.
// Nu sterge continutul anterior al fisierului.
jQuery(document).ready(function($) {
    const $filtru = $('#filtru-specialitate');
    const $carduri = $('#lista-servicii .serviciu-col');
    const $faraServicii = $('#fara-servicii');

    $filtru.on('change', function() {
        const optiune = $(this).val();
        let vizibile = 0;

        $carduri.each(function() {
            const disponibil = $(this).attr('data-disponibil') === '1';
            const pret = Number($(this).attr('data-pret'));

            const seAfiseaza =
                optiune === 'toate' ||
                (optiune === 'disponibile' && disponibil) ||
                (optiune === 'pret-maxim-200' && pret <= 200);
            $(this).toggle(seAfiseaza);

            if (seAfiseaza) {
                vizibile++;
            }
        });

        $faraServicii.toggleClass('d-none', vizibile !== 0);
    });

    $('#buton-informatii').on('click', function() {
        const $mesaj = $('#mesaj-informatii');
        const eraAscuns = $mesaj.prop('hidden');

        $mesaj.prop('hidden', !eraAscuns);
        $(this).attr('aria-expanded', String(eraAscuns));
    });
});