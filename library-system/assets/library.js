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

        if (form.action.endsWith('/loans/return.php')
            && !window.confirm('Mark this book as returned?')) {
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
})();