<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    @foreach($products as $product)
        <div class="product-card">

            <a href="{{ route('product.show', $product) }}">
                <img
                    src="{{ $product->main_image
                            ? asset('storage/' . $product->main_image)
                            : asset('images/fallback.png') }}"
                    class="product-image"
                    alt="{{ $product->name }}">
            </a>

            <div class="product-content p-4">
                <h3 class="product-title">{{ $product->name }}</h3>

                {{-- UNIVERSAL SPECS PREVIEW (первые 3 характеристики) --}}
                @php
                    $specs = is_array($product->specs) ? $product->specs : [];
                    // возьмём максимум 3 пары key => value
                    $previewSpecs = array_slice($specs, 0, 3, true);
                @endphp

                @if(!empty($previewSpecs))
                    <ul class="product-spec space-y-1">
                        @foreach($previewSpecs as $label => $value)
                            <li>
                                <span class="text-gray-400 text-xs uppercase">
                                    {{ $label }}:
                                </span>
                                <span class="text-gray-200 text-sm">
                                    {{ $value }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <p class="product-price mt-3">
                    ${{ number_format($product->price, 0) }}
                </p>

                @php
                    $pcCategories = ['Gaming PCs', 'Workstations', 'Office PCs'];
                    $isPc = $product->category && in_array($product->category->name, $pcCategories);
                @endphp

                <div class="flex justify-between mt-4 gap-2">

                    <a href="{{ route('product.show', $product) }}"
                       class="details-btn">
                        Details
                    </a>

                    {{-- FAVORITE --}}
                    <form method="POST"
                          action="{{ route('favorites.add', $product->id) }}"
                          data-favorite-form="add">
                        @csrf
                        <button class="gray-btn px-3 py-2 rounded text-sm">
                            Favorite
                        </button>
                    </form>

                    {{-- CART --}}
                    <form method="POST"
                          action="{{ route('cart.add', $product->id) }}"
                          data-cart-form="add">
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



