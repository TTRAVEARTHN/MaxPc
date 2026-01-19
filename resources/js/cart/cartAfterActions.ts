document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll<HTMLFormElement>('form[data-cart-form]');
    if (!forms.length) return;


    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : '';

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
                    if (!res.ok) throw new Error('Cart action failed');
                    return res.json().catch(() => ({}));
                })

                .then(() => {
                    // просто перезагружаем страницу БЕЗ новой записи в истории
                    window.location.reload();
                })
                .catch(err => {
                    console.error('Cart action error:', err);
                });
        });
    });
});
