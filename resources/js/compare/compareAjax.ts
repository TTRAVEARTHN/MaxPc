function syncCompareBadge() {
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
                el.textContent = String(count);
                el.classList.remove('hidden');
            } else {
                el.textContent = '';
                el.classList.add('hidden');
            }
        })
        .catch(err => console.error('Compare count error:', err));
}

document.addEventListener('DOMContentLoaded', () => {
    syncCompareBadge();

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
            const realMethod = spoofMethod ? 'POST' : (form.method || 'POST');
            const actionType = form.dataset.compareForm;   // "add" | "remove" | "clear"

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
                    // редирект на логин (если когда-нибудь добавим проверку)
                    if (data.redirect_to_login && data.login_url) {
                        window.location.href = data.login_url;
                        return;
                    }

                    const onComparePage = window.location.pathname.startsWith('/compare');

                    if (onComparePage) {
                        const wrapper = document.querySelector<HTMLElement>('#compareWrapper');

                        if (actionType === 'remove') {
                            const productId = form.dataset.productId;
                            if (productId) {
                                // удалить ВСЕ ячейки этого товара (th + td)
                                document
                                    .querySelectorAll<HTMLElement>(
                                        `[data-compare-product-id="${productId}"]`
                                    )
                                    .forEach(el => el.remove());
                            }
                        }

                        if (actionType === 'clear') {
                            // чистим сразу весь контент таблицы
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

// при возврате "назад"
window.addEventListener('pageshow', () => {
    syncCompareBadge();
});
