export function syncFavoritesBadge(): void {
    const els = document.querySelectorAll<HTMLElement>('.js-favorites-count');
    if (!els.length) return;

    fetch('/favorites/count', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
        .then(res => res.json())
        .then(data => {
            const count = Number(data.count ?? 0);

            els.forEach(el => {
                // ak je aspon 1 oblubeny tak zobrazime badge
                if (count > 0) {
                    el.textContent = String(count);
                    el.classList.remove('hidden');
                } else {
                    // inak skryjeme
                    el.textContent = '';
                    el.classList.add('hidden');
                }
            });
        })
        .catch(err => console.error('Favorites count error:', err));
}

// inicializacia AJAX logiky pre formy obubenych
export function initFavoriteForms(root: ParentNode = document): void {
    // hladame formy v konkretnom kontajneri (napr. katalogGrid)
    const forms = root.querySelectorAll<HTMLFormElement>('form[data-favorite-form]');
    if (!forms.length) return;

    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : '';

    forms.forEach(form => {
        // aby sme nepridali handler viac krat
        if ((form as any)._favoritesBound) {
            return;
        }
        (form as any)._favoritesBound = true;

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const spoofMethod =
                (form.querySelector('input[name="_method"]') as HTMLInputElement | null)?.value;
            const realMethod = spoofMethod ? 'POST' : (form.method || 'POST');
            const actionType = form.dataset.favoriteForm; // "add" | "remove"

            //Tento kod bol vytvoreny s pomocou AI
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
                    // neautorizovany user  presmerujeme na login
                    if (data.redirect_to_login && data.login_url) {
                        window.location.href = data.login_url;
                        return;
                    }

                    // ak ide o odstranenie obubeneho na stranke favorites
                    if (actionType === 'remove') {
                        const item = form.closest<HTMLElement>('.favorite-item, .product-card');
                        if (item) item.remove();

                        const wrapper = document.querySelector<HTMLElement>('#favoritesWrapper');
                        const hasItems = wrapper?.querySelectorAll('.favorite-item, .product-card').length ?? 0;
                        if (wrapper && !hasItems) {
                            wrapper.innerHTML =
                                '<p id="favoritesEmptyMessage" class="text-gray-400">You have no favorite products yet.</p>';
                        }
                    }

                    // po kazdej akcii aktualizujeme badge
                    syncFavoritesBadge();
                })
                .catch(err => console.error('Favorite AJAX error:', err));
        });
    });
}

// pri prvom nacitani stranky nastavime badge a AJAX
document.addEventListener('DOMContentLoaded', () => {
    syncFavoritesBadge();
    initFavoriteForms(document);
});

// pri navrate z bfcache znovu zosynchronizujeme badge
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        syncFavoritesBadge();
    }
});
