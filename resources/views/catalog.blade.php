@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto px-6 py-12 text-white">

        {{-- =============================== --}}
        {{-- PAGE HEADER --}}
        {{-- =============================== --}}
        <h1 class="page-title mb-2">Product Catalog</h1>
        <p class="page-subtitle mb-8">
            Browse our selection of high-performance PCs and peripherals
        </p>

        @php
            $currentCategory = request('category');
            $currentSort = request('sort', 'default');
        @endphp

        {{-- =============================== --}}
        {{-- CATEGORY FILTERS --}}
        {{-- =============================== --}}
        <div class="flex gap-3 mb-6">

            {{-- ALL PRODUCTS --}}
            <a href="{{ route('catalog.index', ['sort' => $currentSort !== 'default' ? $currentSort : null]) }}"
               data-category-link
               data-category-id=""
               class="{{ $currentCategory ? 'filter-btn' : 'filter-btn-active' }}">
                All Products
            </a>

            @foreach($categories as $cat)
                <a href="{{ route('catalog.index', [
                        'category' => $cat->id,
                        'sort'     => $currentSort !== 'default' ? $currentSort : null
                     ]) }}"
                   data-category-link
                   data-category-id="{{ $cat->id }}"
                   class="{{ (string)$currentCategory === (string)$cat->id ? 'filter-btn-active' : 'filter-btn' }}">
                    {{ $cat->name }}
                </a>
            @endforeach

        </div>

        {{-- =============================== --}}
        {{-- SORTING --}}
        {{-- =============================== --}}
        <div class="flex items-center gap-3 mb-6">

            <span class="text-gray-400">Sort by:</span>

            <form method="GET" id="sortForm">
                @if($currentCategory)
                    <input type="hidden" name="category" value="{{ $currentCategory }}">
                @endif

                <select name="sort" class="sort-select"
                        onchange="document.querySelector('#sortForm').submit()">

                    <option value="default" {{ $currentSort === 'default' ? 'selected' : '' }}>
                        Default
                    </option>
                    <option value="price_asc" {{ $currentSort === 'price_asc' ? 'selected' : '' }}>
                        Price ↑
                    </option>
                    <option value="price_desc" {{ $currentSort === 'price_desc' ? 'selected' : '' }}>
                        Price ↓
                    </option>
                    <option value="newest" {{ $currentSort === 'newest' ? 'selected' : '' }}>
                        Newest
                    </option>

                </select>
            </form>

            <span class="text-gray-500 ml-auto" id="productCount">
                {{ $products->total() }} products
            </span>

        </div>

        {{-- =============================== --}}
        {{-- PRODUCT GRID (AJAX-CONTAINER) --}}
        {{-- =============================== --}}
        <div id="catalogGrid">
            @include('partials.catalog_grid', ['products' => $products])
        </div>

        {{-- =============================== --}}
        {{-- PAGINATION / LOAD MORE (пока можно оставить как было или убрать) --}}
        {{-- =============================== --}}
        {{--
        <div class="flex justify-center mt-10">
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}"
                   class="load-more-btn">
                    Load More
                </a>
            @endif
        </div>
        --}}

    </div>

@endsection
