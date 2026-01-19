function syncCompareCount() {
    const el = document.querySelector<HTMLElement>('#compareCount');
    if (!el) return;

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

            if (count > 0) {
                if (count > 0) {
                    el.textContent = String(count);
                    el.classList.remove('hidden');
                } else {
                    el.textContent = '';
                    el.classList.add('hidden');
                }

            } else {
                el.classList.add('hidden');
            }
        })
        .catch(err => console.error('Compare count error:', err));
}

document.addEventListener('DOMContentLoaded', () => {
    syncCompareCount();
});

window.addEventListener('pageshow', () => {
    // при возврате "назад" из history
    syncCompareCount();
});
