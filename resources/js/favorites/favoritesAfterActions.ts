document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll<HTMLFormElement>('form[data-favorite-form="remove"]');
    if (!forms.length) return;

    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : '';

    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const spoofMethod = (form.querySelector('input[name="_method"]') as HTMLInputElement | null)?.value;
            const realMethod = spoofMethod ? 'POST' : (form.method || 'POST');

            fetch(form.action, {
                method: realMethod.toUpperCase(),
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            })
                .then(res => {
                    if (!res.ok) throw new Error('Favorite action failed');
                    return res.json().catch(() => ({}));
                })
                .then(() => {
                    const item = form.closest<HTMLDivElement>('.favorite-item');
                    if (item) item.remove();

                    const emptyMsg = document.querySelector<HTMLElement>('#favoritesEmptyMessage');
                    const stillItems = document.querySelectorAll('.favorite-item').length;

                    if (!stillItems) {
                        if (emptyMsg) {
                            emptyMsg.classList.remove('hidden');
                        } else {
                            const p = document.createElement('p');
                            p.id = 'favoritesEmptyMessage';
                            p.className = 'text-gray-400';
                            p.textContent = 'You have no favorite products yet.';
                            const container = document.querySelector('main') || document.body;
                            container.appendChild(p);
                        }
                    }
                })
                .catch(err => console.error('Favorite action error:', err));
        });
    });
});
