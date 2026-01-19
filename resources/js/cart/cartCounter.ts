document.addEventListener("DOMContentLoaded", () => {
    const cartCountEl = document.querySelector<HTMLElement>('#cartCount');

    if (!cartCountEl) return;

    // соберём данные из сервера
    fetch('/cart/count', {
        method: 'GET',
        headers: {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        }
    })
        .then(res => res.json())
        .then(data => {
            const count = Number(data.count ?? 0);

            if (count > 0) {
                cartCountEl.textContent = String(count);
                cartCountEl.classList.remove('hidden');
            } else {
                cartCountEl.classList.add('hidden');
            }
        })
        .catch(err => console.error('Cart counter sync error:', err));
});

// реакция на bfcache (назад/вперёд)
window.addEventListener("pageshow", (event) => {
    if (event.persisted) {
        fetch('/cart/count')
            .then(res => res.json())
            .then(data => {
                const cartCountEl = document.querySelector<HTMLElement>('#cartCount');
                const count = Number(data.count ?? 0);

                if (!cartCountEl) return;

                if (count > 0) {
                    cartCountEl.textContent = String(count);
                    cartCountEl.classList.remove('hidden');
                } else {
                    cartCountEl.classList.add('hidden');
                }
            });
    }
});

