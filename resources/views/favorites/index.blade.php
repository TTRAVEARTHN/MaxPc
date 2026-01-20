@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto px-6 py-10 text-white">

        <h1 class="page-title mb-6">My Favorites</h1>

        @if(session('success'))
            <div class="alert-success mb-4">{{ session('success') }}</div>
        @endif

        @if($favorites->isEmpty())
            <p class="text-gray-400">You have no favorite products yet.</p>
        @else
            {{-- ОБЁРТКА, с которой работают скрипты (favoritesAjax.ts) --}}
            <div id="favoritesWrapper" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                @foreach($favorites as $fav)
                    @php($product = $fav->product)
                    @if(!$product) @continue @endif

                    {{-- добавил favorite-item, чтобы favoritesAfterActions.ts мог удалить карточку --}}
                    <div class="product-card favorite-item">

                        <a href="{{ route('product.show', $product) }}">
                            <img src="{{ $product->main_image
                                        ? asset('storage/' . $product->main_image)
                                        : asset('images/fallback.png') }}"
                                 class="product-image"
                                 alt="{{ $product->name }}">
                        </a>

                        <div class="product-content p-4">
                            <h3 class="product-title">{{ $product->name }}</h3>
                            <p class="product-price">
                                ${{ number_format($product->price, 0) }}
                            </p>

                            <div class="flex justify-between mt-4 gap-2">
                                <a href="{{ route('product.show', $product) }}"
                                   class="details-btn">
                                    Details
                                </a>

                                {{-- ВАЖНО: data-favorite-form="remove" --}}
                                <form method="POST"
                                      action="{{ route('favorites.remove', $product->id) }}"
                                      data-favorite-form="remove">
                                    @csrf
                                    @method('DELETE')
                                    <button class="gray-btn px-3 py-2 rounded text-sm">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
        @endif

    </div>

@endsection
