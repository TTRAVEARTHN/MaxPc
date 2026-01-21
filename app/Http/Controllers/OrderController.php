<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function checkout()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()
                ->route('login.form')
                ->with('error', 'Please, log in first.');
        }


        $cart = Cart::with('items.product')
            ->where('user_id', $user->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        return view('checkout', compact('cart'));
    }

    public function placeOrder(Request $request)
    {

        $user = Auth::user();

        // 1) проверяем наличие адреса
        if (!$user->address) {
            return redirect()
                ->route('account')   // твой роут профиля
                ->with(
                    'error',
                    'Please add your shipping address in your profile before placing an order.'
                );
        }


        // 2) загружаем корзину
        $cart = Cart::with('items.product')
            ->where('user_id', $user->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // 3) считаем сумму
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->quantity * $item->product->price;
        }

        // 4) создаём заказ
        $order = Order::create([
            'user_id'     => $user->id,
            'total_price' => $total,
            'status'      => 'pending',
        ]);

        // 5) позиции заказа
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $item->product->price,
            ]);
        }

        // 6) чистим корзину
        $cart->items()->delete();

        return redirect()
            ->route('home')
            ->with('success', 'Order placed successfully!');
    }
}
