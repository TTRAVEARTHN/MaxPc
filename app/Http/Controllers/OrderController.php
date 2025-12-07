<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Show checkout page
     */
    public function checkout()
    {
        $user = Auth::user();

        // user must be logged in
        if (!$user) {
            return redirect()->route('login.form')->with('error', 'Please, log in first.');
        }

        // get user's cart
        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        return view('checkout', compact('cart'));
    }

    /**
     * Process order + clear cart
     */
    public function placeOrder(Request $request)
    {
        $user = Auth::user();

        // get cart
        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // calculate total
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->quantity * $item->product->price;
        }

        // create order
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $total,
            'status' => 'pending', // pending, paid, shipped, etc.
        ]);

        // create order items
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id'  => $order->id,
                'product_id'=> $item->product_id,
                'quantity'  => $item->quantity,
                'price'     => $item->product->price,
            ]);
        }

        // clear cart
        $cart->items()->delete();

        return redirect()->route('home')->with('success', 'Order placed successfully!');
    }
}
