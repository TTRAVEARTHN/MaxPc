document.addEventListener("DOMContentLoaded", () => {
    const categoryLinks = document.querySelectorAll<HTMLAnchorElement>('[data-category-link]');
    const catalogGridRaw   = document.querySelector<HTMLDivElement>('#catalogGrid');
    const productCountRaw  = document.querySelector<HTMLElement>('#productCount');
    const sortSelect       = document.querySelector<HTMLSelectElement>('#sortSelect');


    // если нужных элементов нет — выходим и вообще ничего не делаем
    if (!catalogGridRaw || !productCountRaw) {
        return;
    }


    // после проверки TS понимает, что это уже НЕ null
    const catalogGrid  = catalogGridRaw;
    const productCount = productCountRaw;

    // общая функция загрузки каталога по URL
    function loadCatalog(url: string): void {
        fetch(url, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json",
            },
        })
            .then((response: Response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data: any) => {
                catalogGrid.innerHTML = data.html;
                productCount.textContent = `${data.total} products`;
                window.history.pushState({}, "", url);
            })
            .catch(err => {
                console.error("Catalog AJAX error:", err);
            });
    }


    if (categoryLinks.length) {
        categoryLinks.forEach(link => {
            link.addEventListener("click", (e) => {
                e.preventDefault();

                const url = link.href;

                // переключаем активную категорию
                categoryLinks.forEach(l => {
                    l.classList.remove("filter-btn-active");
                    l.classList.add("filter-btn");
                });
                link.classList.remove("filter-btn");
                link.classList.add("filter-btn-active");

                loadCatalog(url);
            });
        });
    }



    if (sortSelect) {
        sortSelect.addEventListener("change", (e) => {
            e.preventDefault();

            const params = new URLSearchParams(window.location.search);
            const selectedSort = sortSelect.value;

            if (selectedSort === "default") {
                params.delete("sort");
            } else {
                params.set("sort", selectedSort);
            }

            const baseUrl = "/catalog";
            const query   = params.toString();
            const url     = query ? `${baseUrl}?${query}` : baseUrl;

            loadCatalog(url);
        });
    }
});
