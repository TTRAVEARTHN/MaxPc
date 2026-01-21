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

        // user musi byt prihlaseny
        if (!$user) {
            return redirect()->route('login.form')->with('error', 'Please, log in first.');
        }

        // nacitanie kosika daneho usera aj s polozkami a produktmi
        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();

        // redirect ak nema nic v kosiku
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        return view('checkout', compact('cart'));
    }


    public function placeOrder(Request $request)
    {
        $user = Auth::user();

        // nacitame kosik aj s produktmi
        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // vypocet celkovej sumy objednavky
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->quantity * $item->product->price;
        }

        // vytvorenie objednavky
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $total,
            'status' => 'pending',
        ]);

        // vytvorenie poloziek objednavky z poloziek kosika
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id'  => $order->id,
                'product_id'=> $item->product_id,
                'quantity'  => $item->quantity,
                'price'     => $item->product->price,
            ]);
        }

        // vycistenie kosika po vytvoreni objednavky
        $cart->items()->delete();

        return redirect()->route('home')->with('success', 'Order placed successfully!');
    }
}
