import { initCartForms } from '../cart/cartAfterActions';
import { initFavoriteForms } from '../favorites/favoritesAjax';

document.addEventListener('DOMContentLoaded', () => {
    const categoryLinks = document.querySelectorAll<HTMLAnchorElement>('[data-category-link]');

    const catalogGridRaw   = document.querySelector<HTMLDivElement>('#catalogGrid');
    const productCountRaw  = document.querySelector<HTMLElement>('#productCount');
    const sortSelect       = document.querySelector<HTMLSelectElement>('#sortSelect');
    const catalogInfo      = document.querySelector<HTMLElement>('#catalogInfo');
    const loadMoreWrapper  = document.querySelector<HTMLDivElement>('#loadMoreWrapper');

    // если нет грида или счетчика  дальше ничего не делаем
    if (!catalogGridRaw || !productCountRaw) {
        return;
    }

    // дальше работаем уже с не null переменными
    const catalogGrid: HTMLDivElement = catalogGridRaw;
    const productCount: HTMLElement   = productCountRaw;

    function updateInfoAndLoadMore(data: any): void {
        if (catalogInfo) {
            catalogInfo.textContent = `Showing ${data.from} to ${data.to} of ${data.total} results`;
        }

        if (!loadMoreWrapper) return;

        if (data.hasMore && data.nextPageUrl) {
            loadMoreWrapper.innerHTML = `
                <a id="loadMoreBtn"
                   href="${data.nextPageUrl}"
                   data-next-url="${data.nextPageUrl}"
                   class="load-more-btn">
                    Load more
                </a>
            `;
            attachLoadMore();
        } else {
            loadMoreWrapper.innerHTML = '';
        }
    }

    function loadCatalog(url: string): void {
        const apiUrl = url.includes('?') ? `${url}&ajax=1` : `${url}?ajax=1`;

        fetch(apiUrl, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
            },
        })
            .then((response: Response) => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then((data: any) => {
                // заменяем карты товаров
                catalogGrid.innerHTML = data.html;
                productCount.textContent = `${data.total} products`;

                // перевешиваем AJAX-обработчики
                initCartForms(catalogGrid);
                initFavoriteForms(catalogGrid);

                // обновляем текст и кнопку Load more
                updateInfoAndLoadMore(data);

                // в историю пишем URL без ajax=1
                window.history.pushState({}, '', url);
            })
            .catch(err => {
                console.error('Catalog AJAX error:', err);
            });
    }

    function attachLoadMore(): void {
        if (!loadMoreWrapper) return;

        const btn = loadMoreWrapper.querySelector<HTMLAnchorElement>('#loadMoreBtn');
        if (!btn) return;

        btn.addEventListener('click', e => {
            e.preventDefault();

            // защита от двойного клика
            if (btn.dataset.loading === '1') return;
            btn.dataset.loading = '1';

            const nextUrl = btn.dataset.nextUrl;
            if (!nextUrl) return;

            const apiUrl = nextUrl.includes('?') ? `${nextUrl}&ajax=1` : `${nextUrl}?ajax=1`;

            fetch(apiUrl, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                },
            })
                .then(res => {
                    if (!res.ok) throw new Error('Load more failed');
                    return res.json();
                })
                .then((data: any) => {
                    // добавляем новые карточки в конец
                    catalogGrid.insertAdjacentHTML('beforeend', data.html);

                    if (catalogInfo) {
                        catalogInfo.textContent = `Showing ${data.from} to ${data.to} of ${data.total} results`;
                    }


                    initCartForms(catalogGrid);
                    initFavoriteForms(catalogGrid);


                    if (data.hasMore && data.nextPageUrl) {
                        btn.dataset.nextUrl = data.nextPageUrl;
                        btn.setAttribute('href', data.nextPageUrl);
                        btn.dataset.loading = '0';
                    } else {

                        loadMoreWrapper.innerHTML = '';
                    }
                })
                .catch(err => {
                    console.error('Load more AJAX error:', err);
                });
        });
    }



    if (categoryLinks.length) {
        categoryLinks.forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();

                const url = link.href;

                // активная кнопка категории
                categoryLinks.forEach(l => {
                    l.classList.remove('filter-btn-active');
                    l.classList.add('filter-btn');
                });
                link.classList.remove('filter-btn');
                link.classList.add('filter-btn-active');

                // при смене категории всегда на первую страницу
                const cleanUrl = url
                    .replace(/([?&])page=\d+/i, '$1')
                    .replace(/[?&]$/, '');

                loadCatalog(cleanUrl);
            });
        });
    }



    if (sortSelect) {
        sortSelect.addEventListener('change', e => {
            e.preventDefault();

            const params = new URLSearchParams(window.location.search);
            const selectedSort = sortSelect.value;

            if (selectedSort === 'default') {
                params.delete('sort');
            } else {
                params.set('sort', selectedSort);
            }

            // при смене сортировки тоже сбрасываем page
            params.delete('page');

            const baseUrl = '/catalog';
            const query = params.toString();
            const url = query ? `${baseUrl}?${query}` : baseUrl;

            loadCatalog(url);
        });
    }


    // привязываем load more при первом рендере
    attachLoadMore();
});
