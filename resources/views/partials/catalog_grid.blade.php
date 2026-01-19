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


                <div class="flex justify-between mt-4 gap-2">

                    <a href="{{ route('product.show', $product) }}"
                       class="details-btn">
                        Details
                    </a>

                    <form method="POST" action="{{ route('compare.add', $product->id) }}">
                        @csrf
                        <button type="submit" class="gray-btn px-3 py-1 text-sm">
                            Compare
                        </button>
                    </form>

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



