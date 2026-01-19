function syncCartCount() {
    const cartCountEl = document.querySelector<HTMLElement>('#cartCount');
    if (!cartCountEl) return;

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
}

document.addEventListener("DOMContentLoaded", () => {
    syncCartCount();
});

window.addEventListener("pageshow", (event) => {
    // если страница вернулась из bfcache (назад/вперёд) — пересинхронизируем
    if (event.persisted) {
        syncCartCount();
    }
});

