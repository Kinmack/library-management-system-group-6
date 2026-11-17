(() => {
    'use strict';

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || event.defaultPrevented) {
            return;
        }

        if (form.method.toLowerCase() !== 'post') {
            return;
        }

        const confirmation = form.dataset.confirm
            || (form.action.endsWith('/loans/return.php') ? 'Mark this book as returned?' : '');

        if (confirmation !== '' && !window.confirm(confirmation)) {
            event.preventDefault();
            return;
        }

        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        form.dataset.submitting = 'true';
        form.setAttribute('aria-busy', 'true');
        form.querySelectorAll('button[type="submit"], button:not([type])').forEach((button) => {
            button.disabled = true;
        });
    });

    document.querySelectorAll('[data-table-search]').forEach((input) => {
        const table = document.querySelector(input.dataset.tableSearch);

        if (!(table instanceof HTMLTableElement)) {
            return;
        }

        const rows = Array.from(table.querySelectorAll('tbody tr[data-search-row]'));
        const count = document.querySelector(input.dataset.countTarget);
        const emptyRow = table.querySelector('[data-filter-empty]');
        const singular = count && count.dataset.singular;
        const plural = count && count.dataset.plural;

        const updateResults = () => {
            const query = input.value.trim().toLocaleLowerCase();
            let visible = 0;

            rows.forEach((row) => {
                const matches = row.textContent.toLocaleLowerCase().includes(query);
                row.hidden = !matches;
                if (matches) {
                    visible += 1;
                }
            });

            if (count) {
                count.textContent = `${visible} ${visible === 1 ? singular : plural}`;
            }
            if (emptyRow) {
                emptyRow.hidden = visible > 0;
            }
        };

        input.addEventListener('input', updateResults);
        updateResults();
    });
})();