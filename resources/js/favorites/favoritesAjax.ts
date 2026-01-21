function syncFavoritesBadge(): void {
    const el = document.querySelector<HTMLElement>('#favoritesCount');
    // ak nie je badge v DOM nema zmysel robit request
    if (!el) return;

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

            if (count > 0) {
                el.textContent = String(count);
                el.classList.remove('hidden');
            } else {
                el.textContent = '';
                el.classList.add('hidden');
            }
        })
        .catch(err => console.error('Favorites count error:', err));
}

document.addEventListener('DOMContentLoaded', () => {
    // сразу подтягиваем счётчик
    syncFavoritesBadge();

    // naviazeme AJAX spravanie na vsetky formy pre favorites
    const forms = document.querySelectorAll<HTMLFormElement>('form[data-favorite-form]');
    if (!forms.length) return;

    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : '';

    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const spoofMethod = (form.querySelector('input[name="_method"]') as HTMLInputElement | null)?.value;
            const realMethod = spoofMethod ? 'POST' : (form.method || 'POST');
            const actionType = form.dataset.favoriteForm; // "add" | "remove"

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

                    // odstranenie karty zo stranky obubenych
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

                    // po kazdej akcii aktualizujeme badge v headeri
                    syncFavoritesBadge();
                })
                .catch(err => console.error('Favorite AJAX error:', err));
        });
    });
});

// pri navrate cez "back" znovu zosynchronizujeme badge
window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        syncFavoritesBadge();
    }
});
