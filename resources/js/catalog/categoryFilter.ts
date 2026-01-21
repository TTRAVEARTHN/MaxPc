import { initCartForms } from '../cart/cartAfterActions';
import { initFavoriteForms } from '../favorites/favoritesAjax';

document.addEventListener("DOMContentLoaded", () => {

    const categoryLinks  = document.querySelectorAll<HTMLAnchorElement>('[data-category-link]');
    const catalogGrid    = document.querySelector<HTMLDivElement>('#catalogGrid');
    const productCount   = document.querySelector<HTMLElement>('#productCount');
    const sortSelect     = document.querySelector<HTMLSelectElement>('#sortSelect');
    const catalogInfo    = document.querySelector<HTMLElement>('#catalogInfo');
    const loadMoreWrapper = document.querySelector<HTMLDivElement>('#loadMoreWrapper');

    // ak chyba grid alebo citac tak nema zmysel pokracovat
    if (!catalogGrid || !productCount) {
        return;
    }

    // pomocna funkcia pre info text a load more tlacidlo
    function updateInfoAndLoadMore(data: any): void {

        if (catalogInfo) {
            catalogInfo.textContent = `Showing ${data.from} to ${data.to} of ${data.total} results`;
        }

        if (!loadMoreWrapper) return;

        // ak su este dalsie stranky zobrazime tlacidlo
        if (data.hasMore && data.nextPageUrl) {
            loadMoreWrapper.innerHTML = `
                <a id="loadMoreBtn"
                   href="${data.nextPageUrl}"
                   data-next-url="${data.nextPageUrl}"
                   class="load-more-btn">
                    Load more
                </a>
            `;
            attachLoadMore(); // naviazeme listener
        } else {
            // inak wrapper vycistime
            loadMoreWrapper.innerHTML = '';
        }
    }

    // univerzalne nacitanie katalogu (pre kategoriu a sort) – nahradza obsah
    function loadCatalog(url: string): void {
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
                // nahradime vsetky kartu v gride
                catalogGrid.innerHTML = data.html;
                productCount.textContent = `${data.total} products`;

                // po vymene HTML znova naviazeme AJAX na cart a favorites
                initCartForms(catalogGrid);
                initFavoriteForms(catalogGrid);

                // prepiseme info text a load more
                updateInfoAndLoadMore(data);

                // do historie zapiseme cisty URL bez ajax=1
                window.history.pushState({}, "", url);
            })
            .catch(err => {
                console.error("Catalog AJAX error:", err);
            });
    }

    // load more – prida dalsiu stranku na koniec
    function attachLoadMore(): void {
        if (!loadMoreWrapper) return;

        const btn = loadMoreWrapper.querySelector<HTMLAnchorElement>('#loadMoreBtn');
        if (!btn) return;

        btn.addEventListener('click', (e) => {
            e.preventDefault();

            // ochrana proti dvojkliku
            if (btn.dataset.loading === '1') return;
            btn.dataset.loading = '1';

            const nextUrl = btn.dataset.nextUrl;
            if (!nextUrl) return;

            const apiUrl = nextUrl.includes('?') ? `${nextUrl}&ajax=1` : `${nextUrl}?ajax=1`;

            fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
            })
                .then(res => {
                    if (!res.ok) throw new Error('Load more failed');
                    return res.json();
                })
                .then((data: any) => {
                    // pridame nove karty za existujuce
                    catalogGrid.insertAdjacentHTML('beforeend', data.html);

                    // aktualizujeme text s rozsahom
                    if (catalogInfo) {
                        catalogInfo.textContent =
                            `Showing ${data.from} to ${data.to} of ${data.total} results`;
                    }

                    // znova naviazeme AJAX na nove formy
                    initCartForms(catalogGrid);
                    initFavoriteForms(catalogGrid);

                    // ak su este dalsie stranky, len posunieme next-url
                    if (data.hasMore && data.nextPageUrl) {
                        btn.dataset.nextUrl = data.nextPageUrl;
                        btn.setAttribute('href', data.nextPageUrl);
                        btn.dataset.loading = '0';
                    } else {
                        // ak nie, tlacidlo skryjeme
                        loadMoreWrapper.innerHTML = '';
                    }
                })
                .catch(err => {
                    console.error('Load more AJAX error:', err);
                });
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

                // pri zmene kategorie ideme na prvu stranu (bez page parametra)
                const cleanUrl = url.replace(/([?&])page=\d+/i, '$1').replace(/[?&]$/, '');

                loadCatalog(cleanUrl);
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

            // pri sortovani tiez ideme na prvu stranu
            params.delete('page');

            const baseUrl = "/catalog";
            const query   = params.toString();
            const url     = query ? `${baseUrl}?${query}` : baseUrl;

            loadCatalog(url);
        });
    }

    // naviazeme load more aj po prvom nacitani stranky
    attachLoadMore();
});
