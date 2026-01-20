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

        //return view('cart', compact('items', 'subtotal', 'tax', 'total'));

        $response = response()->view('cart', compact('items', 'subtotal', 'tax', 'total'));

        return $response
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Add product to cart
     */
    public function add(Request $request, Product $product)
    {
        if (!Auth::check()) {
            if ($request->boolean('ajax')) {
                return response()->json([
                    'success' => false,
                    'redirect_to_login' => true,
                    'login_url' => route('login.form'),
                ]);
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
                'quantity' => 1,
            ]);
        }

        // пересчитываем общее количество позиций
        $count = CartItem::where('cart_id', $cart->id)->sum('quantity');

        if ($request->boolean('ajax')) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart!',
                'count'   => $count,
            ]);
        }

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

        // если AJAX – отдаём JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'quantity' => $item->quantity,
            ]);
        }

        // fallback на всякий случай
        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, CartItem $item)
    {
        $item->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'deleted' => true,
            ]);
        }

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

    public function count()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $cart = Cart::with('items')->where('user_id', Auth::id())->first();

        $count = $cart ? $cart->items->sum('quantity') : 0;

        return response()->json(['count' => $count]);
    }
}
