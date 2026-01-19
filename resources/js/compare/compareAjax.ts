document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll<HTMLFormElement>('form[data-compare-form]');
    if (!forms.length) return;

    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : '';


    const compareCountEl = document.querySelector<HTMLElement>('#compareCount');

    forms.forEach(form => {
        form.addEventListener("submit", (e) => {
            e.preventDefault();



            const formData = new FormData(form);
            const spoofMethod = (form.querySelector('input[name="_method"]') as HTMLInputElement | null)?.value;
            const realMethod = spoofMethod ? 'POST' : (form.method || 'POST');

            fetch(form.action, {
                method: realMethod.toUpperCase(),
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            })
                .then(res => {
                    if (!res.ok) throw new Error('Compare action failed');
                    return res.json().catch(() => ({}));
                })

                .then(data => {
                    // если контроллер вернул count
                    if (compareCountEl && typeof data.count !== 'undefined') {
                        const count = Number(data.count) || 0;

                        if (count > 0) {
                            compareCountEl.textContent = String(count);
                            compareCountEl.classList.remove('hidden');
                        } else {
                            compareCountEl.classList.add('hidden');
                        }
                    }

                    // если мы на странице /compare – просто перезагружаем,
                    // но БЕЗ доп. записи в истории
                    if (window.location.pathname.startsWith('/compare')) {
                        window.location.reload();
                    }

                })
                .catch(err => {
                    console.error('Compare AJAX error:', err);
                });
        });

    });




});
