document.addEventListener("DOMContentLoaded", () => {
    const categoryLinks = document.querySelectorAll<HTMLAnchorElement>('[data-category-link]');
    const catalogGrid   = document.querySelector<HTMLDivElement>('#catalogGrid');
    const productCount  = document.querySelector<HTMLElement>('#productCount');

    if (!categoryLinks.length || !catalogGrid || !productCount) {
        return;
    }


    categoryLinks.forEach(link => {
        link.addEventListener("click", (e) => {
            e.preventDefault();

            const url = link.href;

            // переключаем активную категорию по классам
            categoryLinks.forEach(l => {
                l.classList.remove("filter-btn-active");
                l.classList.add("filter-btn");
            });
            link.classList.remove("filter-btn");
            link.classList.add("filter-btn-active");


            fetch(url, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json",
                },
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then(data => {
                    // подменяем грид

                    catalogGrid.innerHTML = data.html;
                    // обновляем счётчик

                    productCount.textContent = `${data.total} products`;


                    window.history.pushState({}, "", url);
                })
                .catch(err => {
                    console.error("Catalog AJAX error:", err);
                });
        });
    });
});
