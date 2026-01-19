@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto px-6 py-10 text-white">

        <h1 class="text-2xl font-semibold mb-6">Shopping Cart</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ============================= --}}
            {{-- LEFT COLUMN — CART ITEMS --}}
            {{-- ============================= --}}
            <div class="lg:col-span-2 space-y-6">

                @forelse($items as $item)
                    <div class="cart-item">

                        {{-- PRODUCT INFO --}}
                        <div class="flex items-center gap-4">

                            <img src="{{ $item->product->main_image
        ? asset('storage/' . $item->product->main_image)
        : asset('images/fallback.png') }}"
                                 class="w-24 h-24 rounded object-cover">

                            <div>
                                <h3 class="cart-title">{{ $item->product->name }}</h3>

                                <p class="cart-spec">
                                    CPU: {{ $item->product->specs['cpu'] ?? '' }}<br>
                                    GPU: {{ $item->product->specs['gpu'] ?? '' }}
                                </p>

                                <p class="cart-price">
                                    ${{ number_format($item->product->price, 0) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">

                            {{-- Minus --}}
                            <form method="POST"
                                  action="{{ route('cart.update', $item->id) }}"
                                  data-cart-form="update">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity"
                                       value="{{ max(1, $item->quantity - 1) }}">
                                <button class="qty-btn">−</button>
                            </form>

                            {{-- ВОТ ЭТОТ СПАН ДОЛЖЕН БЫТЬ--}}

                            <span class="text-lg">
                                {{ $item->quantity }}
                            </span>

                            {{-- Plus --}}
                            <form method="POST"
                                  action="{{ route('cart.update', $item->id) }}"
                                  data-cart-form="update">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity"
                                       value="{{ $item->quantity + 1 }}">
                                <button class="qty-btn">+</button>
                            </form>

                            {{-- Delete --}}
                            <form method="POST"
                                  action="{{ route('cart.remove', $item->id) }}"
                                  data-cart-form="remove">
                                @csrf
                                @method('DELETE')
                                <button class="delete-btn">🗑</button>
                            </form>

                        </div>

                    </div>
                @empty
                    <p class="text-gray-400">Your cart is empty.</p>
                @endforelse

            </div>


            {{-- ============================= --}}
            {{-- RIGHT COLUMN — SUMMARY --}}
            {{-- ============================= --}}
            <div class="cart-summary">

                <h2 class="text-xl font-semibold mb-4">Order Summary</h2>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>

                <div class="summary-row">
                    <span>Tax (20%)</span>
                    <span>{{ number_format($tax, 2) }}</span>
                </div>

                <div class="summary-row">
                    <span>Shipping</span>
                    <span class="text-green-400">FREE</span>
                </div>

                <hr class="border-gray-700 my-4">

                <div class="summary-total">
                    <span>Total</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>

                {{-- LOGIN REQUIRED --}}
                @guest
                    <p class="text-gray-400 mb-3 text-sm">You must log in to place an order</p>

                    <a href="{{ route('login.form') }}"
                       class="checkout-btn text-center">
                        🔐 Log In to Continue
                    </a>

                @else
                    <a href="{{ route('checkout') }}" class="checkout-btn text-center">
                        Proceed to Checkout
                    </a>
                @endguest

                <p class="text-gray-500 text-sm mt-4">
                    Free shipping on all orders · 2-year warranty included
                </p>

            </div>

        </div>

    </div>

@endsection
