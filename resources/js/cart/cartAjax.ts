document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll<HTMLButtonElement>('button[data-cart-add]');
    const cartCountEl = document.querySelector<HTMLElement>('#cartCount');
    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');

    const csrfToken = csrfMeta ? csrfMeta.content : '';

    if (!buttons.length) {
        return;
    }

    buttons.forEach(button => {
        button.addEventListener("click", (e) => {
            e.preventDefault();

            // защита от двойного срабатывания, если вдруг скрипт подключён дважды
            if (button.dataset.loading === '1') {
                return;
            }
            button.dataset.loading = '1';

            const form = button.closest("form") as HTMLFormElement | null;
            if (!form) {
                console.error("Cart button has no parent form");
                button.dataset.loading = '0';
                return;
            }

            const action = form.action;
            const url = action.includes("?") ? `${action}&ajax=1` : `${action}?ajax=1`;

            const formData = new FormData(form);

            fetch(url, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.redirect_to_login && data.login_url) {
                        window.location.href = data.login_url;
                        return;
                    }

                    if (data.success) {
                        if (cartCountEl) {
                            cartCountEl.textContent = String(data.count);
                            cartCountEl.classList.remove("hidden");
                        }
                        console.log(data.message);
                    } else {
                        console.error(data.message || "Error adding to cart");
                    }
                })
                .catch(err => {
                    console.error("Cart AJAX error:", err);
                })
                .finally(() => {
                    button.dataset.loading = '0';
                });
        });
    });
});
