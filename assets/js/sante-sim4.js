/**
 * Simulare Sante - interactiuni pentru catalog.
 * Cod separat de template-ul PHP pentru a nu amesteca responsabilitatile.
 */
jQuery(function($) {
    'use strict';

    const $app = $('#sante4-app');
    if (!$app.length) {
        return;
    }

    const $search = $('#sante4-cautare');
    const $category = $('#sante4-categorie');
    const $stock = $('#sante4-stoc');
    const $reset = $('#sante4-reset');
    const $items = $('#sante4-list .sante4-item');
    const $empty = $('#sante4-empty');
    const $count = $('#sante4-count');
    const $countLabel = $('#sante4-count-label');

    // Datele pentru filtrare sunt deja expuse in atributele data-* din PHP.
    function removeDiacritics(value) {
        return String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }

    function getFilters() {
        return {
            text: removeDiacritics($search.val()),
            category: String($category.val() || 'toate'),
            stock: String($stock.val() || 'toate')
        };
    }

    function cardMatches($item, filters) {
        const name = removeDiacritics($item.attr('data-nume'));
        const category = String($item.attr('data-categorie') || '');
        const available = $item.attr('data-disponibila') === '1';

        const matchesText = !filters.text || name.includes(filters.text);
        const matchesCategory = filters.category === 'toate' || category === filters.category;
        const matchesStock = filters.stock === 'toate' || available;

        return matchesText && matchesCategory && matchesStock;
    }

    function updateCount(visibleCount) {
        $count.text(visibleCount);
        $countLabel.text(visibleCount === 1 ? 'rezultat afisat' : 'rezultate afisate');
    }

    function applyFilters() {
        const filters = getFilters();
        let visible = 0;

        $items.each(function() {
            const $item = $(this);
            const show = cardMatches($item, filters);
            $item.toggleClass('d-none', !show);
            if (show) {
                visible++;
            }
        });

        updateCount(visible);
        $empty.toggleClass('d-none', visible !== 0);
    }

    // Mic debounce ca sa evitam recalcularea pentru fiecare tasta foarte rapid.
    let searchTimer = null;
    $search.on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(applyFilters, 120);
    });

    $category.on('change', applyFilters);
    $stock.on('change', applyFilters);

    $reset.on('click', function() {
        $search.val('');
        $category.val('toate');
        $stock.val('toate');
        applyFilters();
    });

    // Interactiune independenta de catalogul de produse.
    $('#sante4-ajutor').on('click', function() {
        const $answer = $('#sante4-raspuns');
        const isHidden = $answer.prop('hidden');
        $answer.prop('hidden', !isHidden);
        $(this).attr('aria-expanded', isHidden ? 'true' : 'false');
    });

    applyFilters();
});