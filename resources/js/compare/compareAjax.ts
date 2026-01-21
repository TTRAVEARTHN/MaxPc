function syncCompareBadge(): void {
    // vsetky badge pre porovnanie (desktop + mobil)
    const els = document.querySelectorAll<HTMLElement>('.js-compare-count');
    // ak nie je nic na stranke, ukoncime
    if (!els.length) return;

    fetch('/compare/count', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
        .then(res => res.json())
        .then(data => {
            const count = Number(data.count) || 0;

            els.forEach(el => {
                if (count > 0) {
                    el.textContent = String(count);
                    el.classList.remove('hidden');
                } else {
                    el.textContent = '';
                    el.classList.add('hidden');
                }
            });
        })
        .catch(err => console.error('Compare count error:', err));
}

document.addEventListener('DOMContentLoaded', () => {
    // hned po loade vytiahneme aktualny pocet
    syncCompareBadge();

    // najdeme formy pre add/remove/clear
    const forms = document.querySelectorAll<HTMLFormElement>('form[data-compare-form]');
    if (!forms.length) return;

    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : '';

    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const spoofMethod =
                (form.querySelector('input[name="_method"]') as HTMLInputElement | null)?.value;
            // delete/put idea ako post
            const realMethod = spoofMethod ? 'POST' : (form.method || 'POST');

            //typ akcie: add/remove/clear
            const actionType = form.dataset.compareForm;

            // posielame AJAX request
            fetch(form.action, {
                method: realMethod.toUpperCase(),
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            })
                .then(res => res.json().catch(() => ({})))
                .then(data => {
                    // ak by bolo treba login tak redirect
                    if (data.redirect_to_login && data.login_url) {
                        window.location.href = data.login_url;
                        return;
                    }

                    // kontrola ci sme priamo na /compare
                    const onComparePage = window.location.pathname.startsWith('/compare');

                    if (onComparePage) {
                        const wrapper = document.querySelector<HTMLElement>('#compareWrapper');

                        if (actionType === 'remove') {
                            const productId = form.dataset.productId;
                            if (productId) {
                                // odstranime vsetky bunky daneho produktu
                                document
                                    .querySelectorAll<HTMLElement>(
                                        `[data-compare-product-id="${productId}"]`
                                    )
                                    .forEach(el => el.remove());
                            }
                        }

                        if (actionType === 'clear') {
                            // kompletne vycistime tabulku
                            if (wrapper) {
                                wrapper.innerHTML =
                                    '<p class="text-gray-400">You have no products in compare list.</p>';
                            }
                        } else {
                            // если после remove больше нет столбцов товаров — показываем сообщение
                            const remainingCols = document.querySelectorAll(
                                '[data-compare-product-id]'
                            ).length;

                            if (wrapper && remainingCols === 0) {
                                wrapper.innerHTML =
                                    '<p class="text-gray-400">You have no products in compare list.</p>';
                            }
                        }
                    }

                    // обновляем бейдж в хедере
                    syncCompareBadge();
                })
                .catch(err => console.error('Compare AJAX error:', err));
        });
    });
});

// // pri navrate cez tlacidlo back
window.addEventListener('pageshow', () => {
    syncCompareBadge();
});
