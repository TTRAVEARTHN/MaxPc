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
                <span>${{ number_format($cart->items->sum(fn($i) => $i->quantity * $i->product->price), 2) }}</span>
            </div>
        </div>

        {{-- USER DETAILS --}}
        <div class="checkout-box mb-6">
            <h3 class="checkout-title">Your Details</h3>

            <p class="detail-row">Name: <span>{{ auth()->user()->name }}</span></p>
            <p class="detail-row">Email: <span>{{ auth()->user()->email }}</span></p>
            <p class="detail-row">Address: <span>{{ auth()->user()->address ?? 'Not specified' }}</span></p>
        </div>

        {{-- PLACE ORDER BUTTON --}}
        <form method="POST" action="{{ route('checkout.place') }}">
            @csrf
            <button class="checkout-btn-action">Place Order</button>
        </form>

    </div>

@endsection
