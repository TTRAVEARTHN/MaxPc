function formatMoney(value: number): string {
    return value.toLocaleString('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function recalcCartSummary() {
    const itemEls = document.querySelectorAll<HTMLDivElement>('.cart-item');
    let subtotal = 0;

    itemEls.forEach(el => {
        const price = Number(el.dataset.price ?? 0);
        const qtyEl = el.querySelector<HTMLElement>('.cart-qty-display');
        const qty = qtyEl ? Number(qtyEl.textContent ?? 0) : 0;
        subtotal += price * qty;
    });

    const tax = subtotal * 0.20;
    const total = subtotal; // если VAT уже в цене

    const subtotalEl = document.querySelector<HTMLElement>('#cartSubtotal');
    const taxEl      = document.querySelector<HTMLElement>('#cartTax');
    const totalEl    = document.querySelector<HTMLElement>('#cartTotal');

    if (subtotalEl) subtotalEl.textContent = formatMoney(subtotal);
    if (taxEl)      taxEl.textContent      = formatMoney(tax);
    if (totalEl)    totalEl.textContent    = formatMoney(total);
}

function syncCartBadge() {
    const cartCountEl = document.querySelector<HTMLElement>('#cartCount');
    if (!cartCountEl) return;

    fetch('/cart/count', {
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
                cartCountEl.textContent = String(count);
                cartCountEl.classList.remove('hidden');
            } else {
                cartCountEl.classList.add('hidden');
            }
        })
        .catch(err => console.error('Cart badge sync error:', err));
}

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
            const actionType = form.dataset.cartForm; // "update" или "remove"

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
                    const cartItemEl = form.closest<HTMLDivElement>('.cart-item');

                    //  обновление количества
                    if (actionType === 'update' && cartItemEl) {
                        const newQty = Number(
                            (form.querySelector('input[name="quantity"]') as HTMLInputElement).value
                        );

                        // 1) показываем новое кол-во пользователю
                        const qtyDisplay = cartItemEl.querySelector<HTMLElement>('.cart-qty-display');
                        if (qtyDisplay) {
                            qtyDisplay.textContent = String(newQty);
                        }

                        // 2) обновляем hidden-инпуты в формах +/-,
                        //    чтобы следующий клик отправлял правильное значение
                        const updateForms = cartItemEl.querySelectorAll<HTMLFormElement>('form[data-cart-form="update"]');

                        if (updateForms.length === 2) {
                            const minusForm = updateForms[0];
                            const plusForm  = updateForms[1];

                            const minusInput = minusForm.querySelector<HTMLInputElement>('input[name="quantity"]');
                            const plusInput  = plusForm.querySelector<HTMLInputElement>('input[name="quantity"]');

                            if (minusInput) {
                                minusInput.value = String(Math.max(1, newQty - 1));
                            }
                            if (plusInput) {
                                plusInput.value = String(newQty + 1);
                            }
                        }
                    }

                    // удаление товара
                    if (actionType === 'remove' && cartItemEl) {
                        cartItemEl.remove();
                    }
                    //  если корзина опустела — показать сообщение
                    const itemsWrapper = document.querySelector<HTMLDivElement>('#cartItemsWrapper');
                    const remainingItems = itemsWrapper?.querySelectorAll('.cart-item') ?? [];
                    const emptyMsg = document.querySelector<HTMLElement>('#emptyCartMessage');

                    if (remainingItems.length === 0) {
                        if (emptyMsg) {
                            emptyMsg.classList.remove('hidden');
                        } else if (itemsWrapper) {
                            const p = document.createElement('p');
                            p.id = 'emptyCartMessage';
                            p.className = 'text-gray-400';
                            p.textContent = 'Your cart is empty.';
                            itemsWrapper.appendChild(p);
                        }
                    }

                    // пересчёт сумм и бейджа
                    recalcCartSummary();
                    syncCartBadge();
                })
                .catch(err => {
                    console.error('Cart action error:', err);
                });
        });
    });
});
