function formatMoney(value: number): string {
    // jednoduche formatovanie ceny na USD
    return value.toLocaleString('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function recalcCartSummary() {
    // prechadzame vsetky polozky v kosiku a rucne prepocitame sumy
    const itemEls = document.querySelectorAll<HTMLDivElement>('.cart-item');
    let subtotal = 0;

    itemEls.forEach(el => {
        const price = Number(el.dataset.price ?? 0);
        const qtyEl = el.querySelector<HTMLElement>('.cart-qty-display');
        const qty = qtyEl ? Number(qtyEl.textContent ?? 0) : 0;
        subtotal += price * qty;
    });

    const tax = subtotal * 0.20;
    const total = subtotal; // ak je DPH uz v cene, tak total = subtotal

    const subtotalEl = document.querySelector<HTMLElement>('#cartSubtotal');
    const taxEl      = document.querySelector<HTMLElement>('#cartTax');
    const totalEl    = document.querySelector<HTMLElement>('#cartTotal');

    // zapis prepocitanych hodnot do summary casti
    if (subtotalEl) subtotalEl.textContent = formatMoney(subtotal);
    if (taxEl)      taxEl.textContent      = formatMoney(tax);
    if (totalEl)    totalEl.textContent    = formatMoney(total);
}

function syncCartBadge() {

    // najdeme vsetky badge pre kosik (desktop + mobile)
    const cartCountEls = document.querySelectorAll<HTMLElement>('.js-cart-count');
    if (!cartCountEls.length) return;


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

            cartCountEls.forEach(el => {
                // zobrazime alebo skryjeme badge podla poctu
                if (count > 0) {
                    el.textContent = String(count);
                    el.classList.remove('hidden');
                } else {
                    el.textContent = '';
                    el.classList.add('hidden');
                }
            });
        })
        .catch(err => console.error('Cart badge sync error:', err));
}


export function initCartForms(root: ParentNode = document): void {
    // najdeme formy len v ramci daneho rootu
    const forms = root.querySelectorAll<HTMLFormElement>('form[data-cart-form]');
    // ak v root nie su ziadne cart formy tak nic neriesime
    if (!forms.length) return;

    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : '';



    forms.forEach(form => {
        // ochrana aby sme na ten isty form nepridali listener viackrat
        if ((form as any)._cartHandlerAttached) {
            return;
        }
        (form as any)._cartHandlerAttached = true;

        form.addEventListener("submit", (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const spoofMethod = (form.querySelector('input[name="_method"]') as HTMLInputElement | null)?.value;
            const realMethod = spoofMethod ? 'POST' : (form.method || 'POST');
            const actionType = form.dataset.cartForm; // "add" | "update" | "remove"

            // AJAX request

            //Tento kod bol vytvoreny s pomocou AI
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

                    // update mnozstva polozky
                    if (actionType === 'update' && cartItemEl) {
                        const newQty = Number(

                            (form.querySelector('input[name="quantity"]') as HTMLInputElement).value
                        );

                        // zobrazime nove mnozstvo pri polozke
                        const qtyDisplay = cartItemEl.querySelector<HTMLElement>('.cart-qty-display');
                        if (qtyDisplay) {
                            qtyDisplay.textContent = String(newQty);
                        }

                        // aktualizujeme hidden inputy v + a - formach
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

                    // odstranenie polozky z kosika
                    if (actionType === 'remove' && cartItemEl) {
                        cartItemEl.remove();
                    }



                    // ak po operacii nezostali ziadne polozky -> zobrazime hlasku o prazdnom kosiku
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


                    // po akcii prepocitame summary a badge v hlavicke
                    recalcCartSummary();
                    syncCartBadge();
                })
                .catch(err => {
                    console.error('Cart action error:', err);
                });
        });
    });
}

// inicializacia pri prvom nacitani celej stranky
document.addEventListener("DOMContentLoaded", () => {
    // pri kazdom loade stranky zosynchronizujeme badge
    syncCartBadge();

    // naviazeme AJAX na vsetky cart formy v dokumente
    initCartForms(document);
});

// ked sa vratime naspat (bfcache), tak si dotiahneme aktualny pocet z backendu
window.addEventListener("pageshow", (event) => {
    if (event.persisted) {
        syncCartBadge();
    }
});
