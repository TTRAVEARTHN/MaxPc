document.addEventListener("DOMContentLoaded", () => {
    const categoryLinks = document.querySelectorAll<HTMLAnchorElement>('[data-category-link]');
    const catalogGridRaw   = document.querySelector<HTMLDivElement>('#catalogGrid');
    const productCountRaw  = document.querySelector<HTMLElement>('#productCount');
    const sortSelect       = document.querySelector<HTMLSelectElement>('#sortSelect');


    // ak chyba grid alebo citac tak nema zmysel pokracovat
    if (!catalogGridRaw || !productCountRaw) {
        return;
    }


    //vieme ze nie su null
    const catalogGrid  = catalogGridRaw;
    const productCount = productCountRaw;

    // funkcia pre AJAX nacitanie katalogu cez URL
    function loadCatalog(url: string): void {
        // pridame ajax=1 do query
        const apiUrl = url.includes("?") ? `${url}&ajax=1` : `${url}?ajax=1`;

        fetch(apiUrl, {
            method: "GET",
            headers: {
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
                // aktualizujeme HTML katalogu a pocet produktov
                catalogGrid.innerHTML = data.html;
                productCount.textContent = `${data.total} products`;

                // do historiky zapiseme cisty URL bez ajaxu
                window.history.pushState({}, "", url);
            })
            .catch(err => {
                console.error("Catalog AJAX error:", err);
            });
    }


    // klik na kategoriu cez AJAX
    if (categoryLinks.length) {
        categoryLinks.forEach(link => {
            link.addEventListener("click", (e) => {
                e.preventDefault();

                const url = link.href;

                // nastavenie aktivnej kategorie
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



    // zmena sortu cez AJAX
    if (sortSelect) {
        sortSelect.addEventListener("change", (e) => {
            e.preventDefault();

            const params = new URLSearchParams(window.location.search);
            const selectedSort = sortSelect.value;

            // default znamena ze sort odstranime
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
