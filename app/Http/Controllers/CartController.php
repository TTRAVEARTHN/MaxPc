<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Show user's cart page
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $user = Auth::user();

        // Create cart if not exists
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Load items with product relation
        $items = CartItem::where('cart_id', $cart->id)
            ->with('product')
            ->get();

        // Calculate totals
        $subtotal = $items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $tax = $subtotal * 0.20; // просто чтобы показать
        $total = $subtotal;      // итоговая цена уже содержит VAT

        return view('cart', compact('items', 'subtotal', 'tax', 'total'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request, Product $product)
    {


        if (!Auth::check()) {
            // AJAX-ответ
            if ($request->boolean('ajax')) {
                return response()->json([
                    'success' => false,
                    'redirect_to_login' => true,
                    'login_url' => route('login.form'),
                    'message' => 'You must be logged in to use the cart.',
                ], 401);
            }



            return redirect()->route('login.form')
                ->with('error', 'You must be logged in to use the cart.');
        }

        $user = Auth::user();

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();


        if ($item) {
            $item->quantity += 1;
            $item->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 1
            ]);
        }

        // считаем общее количество товаров в корзине
        $totalQty = $cart->items()->sum('quantity');

        // ответ
        if ($request->boolean('ajax')) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart!',
                'count'   => $totalQty,
            ]);
        }

        // обычный случай
        return back()->with('success', 'Product added to cart!');
    }

    /**
     * Update quantity
     */
    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated.');
    }

    /**
     * Remove item
     */
    public function remove(CartItem $item)
    {
        $item->delete();

        return back()->with('success', 'Item removed.');
    }

    /**
     * Clear user cart
     */
    public function clear()
    {
        $user = Auth::user();

        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return back()->with('success', 'Cart cleared.');
    }
}
