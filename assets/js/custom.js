// Adauga blocul acesta la SFARSITUL assets/js/custom.js.
// Functioneaza pe pagina catalog-analize; pe alte pagini nu modifica nimic.
jQuery(document).ready(function($) {
    const $filtru = $('#sante-filtru-stoc');
    const $carduri = $('#sante-lista-analize .sante-col-analiza');
    const $faraRezultate = $('#sante-fara-rezultate');

    if (!$filtru.length) {
        return;
    }

    $filtru.on('change', function() {
        const selectie = $(this).val();
        let vizibile = 0;

        $carduri.each(function() {
            const esteDisponibila = $(this).attr('data-disponibila') === '1';
            const afiseaza = selectie === 'toate' || esteDisponibila;
            $(this).toggleClass('d-none', !afiseaza);
            if (afiseaza) {
                vizibile++;
            }
        });

        $faraRezultate.toggleClass('d-none', vizibile > 0);
    });
});