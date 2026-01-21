@extends('layouts.app')

@section('content')

    <div class="page-container">

        {{-- HEADER --}}
        <h1 class="page-title mb-2">{{ $product->name }}</h1>

        <p class="text-gray-400 mb-8">
            @if($product->category)
                Category:
                <span class="text-blue-400">{{ $product->category->name }}</span>
            @else
                <span class="text-gray-500">Uncategorized</span>
            @endif
        </p>

        <div class="product-layout">

            {{-- ======================= --}}
            {{-- LEFT COLUMN — IMAGE --}}
            {{-- ======================= --}}
            <div>
                <img src="{{ $product->main_image ? asset('storage/' .$product->main_image) : '/images/fallback-pc.png' }}"
                     alt="{{ $product->name }}"
                     class="product-main-img">
            </div>

            {{-- ======================= --}}
            {{-- RIGHT COLUMN — DETAILS --}}
            {{-- ======================= --}}
            <div>

                {{-- PRICE --}}
                <div class="product-price-big mb-6">
                    ${{ number_format($product->price, 2) }}
                </div>

                {{-- DESCRIPTION --}}
                @if($product->description)
                    <p class="product-description mb-6">
                        {{ $product->description }}
                    </p>
                @endif

                {{-- SPECIFICATIONS --}}
                @if(is_array($product->specs))
                    <div class="mb-8">
                        <h2 class="section-title mb-4">Specifications</h2>

                        <ul class="spec-list">
                            @foreach($product->specs as $key => $value)
                                <li class="spec-item">
                                    <span class="font-semibold">{{ strtoupper($key) }}:</span>
                                    {{ $value }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $pcCategories = ['Gaming PCs', 'Workstations', 'Office PCs'];
                    $isPc = $product->category && in_array($product->category->name, $pcCategories);
                @endphp

                {{-- на мобильных кнопки под собой, на шире – в ряд --}}
                <div class="product-actions">

                    <form method="POST"
                          action="{{ route('cart.add', $product->id) }}"
                          data-cart-form="add">
                        @csrf
                        <button class="product-action-btn blue-btn w-full sm:w-auto">
                            Add to Cart
                        </button>
                    </form>

                    <form method="POST"
                          action="{{ route('favorites.add', $product->id) }}"
                          data-favorite-form="add">
                        @csrf
                        <button class="product-action-btn gray-btn w-full sm:w-auto">
                            Favorite
                        </button>
                    </form>

                    @if($isPc)
                        <form method="POST"
                              action="{{ route('compare.add', $product->id) }}"
                              data-compare-form="add">
                            @csrf
                            <button type="submit" class="product-action-btn gray-btn w-full sm:w-auto">
                                Add to Compare
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('catalog.index') }}"
                       class="product-action-btn gray-btn w-full sm:w-auto">
                        Back to Catalog
                    </a>

                </div>

            </div>

        </div>

    </div>

@endsection
