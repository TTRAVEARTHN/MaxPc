<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // ak nie je user prihlaseny, presmerujeme na login
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $user = Auth::user();

        // vytvorime kosik ak este neexistuje pre tohto usera
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // nacitame polozky kosika spolu s produktmi
        $items = CartItem::where('cart_id', $cart->id)
            ->with('product')
            ->get();

        // vypocet medzisuctu
        $subtotal = $items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $tax = $subtotal * 0.20; // просто чтобы показать
        $total = $subtotal;      // итоговая цена уже содержит VAT

        //return view('cart', compact('items', 'subtotal', 'tax', 'total'));

        $response = response()->view('cart', compact('items', 'subtotal', 'tax', 'total'));
        // zakaz cache pre stranku kosika
        return $response
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }


    public function add(Request $request, Product $product)
    {

        if (!Auth::check()) {
            // pre AJAX vraciame JSON s info ze treba login
            if ($request->boolean('ajax')) {
                return response()->json([
                    'success' => false,
                    'redirect_to_login' => true,
                    'login_url' => route('login.form'),
                ]);
            }
            //redirect na login
            return redirect()->route('login.form')
                ->with('error', 'You must be logged in to use the cart.');
        }

        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // hladame existujucu polozku tohto produktu v kosiku
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            // ak existuje, zvysime quantity
            $item->quantity += 1;
            $item->save();
        } else {
            // ak nie, vytvorime novu polozku
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }

        // prepocteme celkovy pocet kusov v kosiku
        $count = CartItem::where('cart_id', $cart->id)->sum('quantity');

        // odpoved pre AJAX
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
            // nepovolime nulu ani zaporne cisla
            'quantity' => 'required|integer|min:1',
        ]);

        $item->update(['quantity' => $request->quantity]);

        // ak ide o AJAX vratime JSON pre live update na stranke
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'quantity' => $item->quantity,
            ]);
        }

        // fallback ak by to slo cez normalny form
        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, CartItem $item)
    {
        $item->delete();
        // JSON odpoved pre AJAX mazanie polozky
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'deleted' => true,
            ]);
        }

        return back()->with('success', 'Item removed.');
    }

    public function clear()
    {
        $user = Auth::user();

        $cart = Cart::where('user_id', $user->id)->first();

        // zmazeme vsetky polozky kosika daneho usera
        if ($cart) {
            $cart->items()->delete();
        }

        return back()->with('success', 'Cart cleared.');
    }

    public function count()
    {
        // neprihlaseny user ma pocet 0
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $cart = Cart::with('items')->where('user_id', Auth::id())->first();

        // ak kosik existuje, scitame pocet kusov
        $count = $cart ? $cart->items->sum('quantity') : 0;

        return response()->json(['count' => $count]);
    }
}
