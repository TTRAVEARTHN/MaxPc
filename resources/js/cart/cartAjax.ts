document.addEventListener("DOMContentLoaded", () => {
    const cartForms = document.querySelectorAll<HTMLFormElement>('form[data-cart-form="add"]');
    const cartCountEl = document.querySelector<HTMLElement>('#cartCount');
    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');

    const csrfToken = csrfMeta ? csrfMeta.content : '';

    if (!cartForms.length) {
        return;
    }
    cartForms.forEach(form => {

        form.addEventListener("submit", (e) => {
            e.preventDefault();

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
                    if (data.redirect_to_login) {
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
                        console.error(data.message || 'Error adding to cart');
                    }
                })

                .catch(err => {
                    console.error("Cart AJAX error:", err);
                });
        });

    });


});
