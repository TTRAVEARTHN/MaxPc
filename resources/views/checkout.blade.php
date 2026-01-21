@extends('layouts.app')

@section('content')

    <div class="max-w-4xl mx-auto text-white py-10 px-4">

        <h1 class="text-3xl font-semibold mb-6">Checkout</h1>

        {{-- ORDER SUMMARY --}}
        <div class="checkout-box mb-6">
            <h3 class="checkout-title">Your Order</h3>

            @foreach($cart->items as $item)
                <div class="checkout-item">
                    <div>
                        <p class="item-name">{{ $item->product->name }}</p>
                        <p class="item-qty">Qty: {{ $item->quantity }}</p>
                    </div>

                    <p class="item-price">
                        ${{ number_format($item->quantity * $item->product->price, 2) }}
                    </p>
                </div>
            @endforeach

            <div class="checkout-total">
                <span>Total:</span>
                <span>
                    ${{ number_format($cart->items->sum(fn($i) => $i->quantity * $i->product->price), 2) }}
                </span>
            </div>
        </div>

        @php
            $user = auth()->user();
        @endphp

        {{-- USER DETAILS --}}
        <div class="checkout-box mb-6">
            <h3 class="checkout-title">Your Details</h3>

            <p class="detail-row">Name: <span>{{ $user->name }}</span></p>
            <p class="detail-row">Email: <span>{{ $user->email }}</span></p>
            <p class="detail-row">
                Address:
                <span>{{ $user->address ?? 'Not specified' }}</span>
            </p>
        </div>

        {{-- WARN, если нет адреса --}}
        @if(!$user->address)
            <div class="mb-4 rounded border border-yellow-600 bg-yellow-900/40 text-yellow-200 px-4 py-2 text-sm">
                You have not specified a shipping address yet.
                <a href="{{ route('account') }}" class="text-blue-400 underline">
                    Go to profile
                </a>
                and add your address before placing an order.
            </div>
        @endif

        {{-- PLACE ORDER BUTTON --}}
        <form method="POST" action="{{ route('checkout.place') }}">
            @csrf
            <button class="checkout-btn-action @if(!$user->address) opacity-60 cursor-not-allowed @endif"
                    @if(!$user->address) disabled @endif>
                Place Order
            </button>
        </form>

    </div>

@endsection
