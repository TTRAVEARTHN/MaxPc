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



        {{-- =============================== --}}
        {{-- CATEGORY FILTERS --}}
        {{-- =============================== --}}
        @php
            $currentCategory = request('category'); // id выбранной категории или null
        @endphp

        <div class="flex gap-3 mb-6">

            {{-- ALL PRODUCTS --}}
            <a href="{{ route('catalog.index') }}"
               class="{{ $currentCategory ? 'filter-btn' : 'filter-btn-active' }}">
                All Products
            </a>

            {{-- КАТЕГОРИИ --}}
            @foreach($categories as $cat)
                <a href="{{ route('catalog.index', ['category' => $cat->id]) }}"
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
                <select name="sort" class="sort-select"
                        onchange="document.querySelector('#sortForm').submit()">

                    <option value="default">Default</option>
                    <option value="price_asc">Price ↑</option>
                    <option value="price_desc">Price ↓</option>
                    <option value="newest">Newest</option>

                </select>
            </form>

            <span class="text-gray-500 ml-auto">
                {{ $products->total() }} products
            </span>

        </div>



        {{-- =============================== --}}
        {{-- PRODUCT GRID --}}
        {{-- =============================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            @foreach($products as $product)

                <div class="product-card">

                    <a href="{{ route('product.show', $product) }}">
                        <img src="{{ asset('storage/' . $product->main_image) }}"
                             class="product-image"
                             alt="{{ $product->name }}">
                    </a>

                    <div class="product-content p-4">

                        <h3 class="product-title">{{ $product->name }}</h3>

                        {{-- SPECS --}}
                        @if(is_array($product->specs))
                            <p class="product-spec">
                                {{ $product->specs['cpu'] ?? '' }}<br>
                                {{ $product->specs['gpu'] ?? '' }}<br>
                                {{ $product->specs['ram'] ?? '' }}
                            </p>
                        @endif

                        <p class="product-price">
                            ${{ number_format($product->price, 0) }}
                        </p>

                        <div class="flex justify-between mt-4">
                            <a href="{{ route('product.show', $product) }}"
                               class="details-btn">
                                Details
                            </a>

                            <form method="POST" action="{{ route('cart.add', $product->id) }}">
                                @csrf
                                <button class="cart-btn">
                                    Add to Cart
                                </button>
                            </form>
                        </div>

                    </div>

                </div>

            @endforeach

        </div>



        {{-- =============================== --}}
        {{-- PAGINATION / LOAD MORE --}}
        {{-- =============================== --}}
        <div class="flex justify-center mt-10">
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}"
                   class="load-more-btn">
                    Load More
                </a>
            @endif
        </div>

    </div>

@endsection
