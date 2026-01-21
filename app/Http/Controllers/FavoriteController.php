<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{

    public function index()
    {
        // ak nie je prihlaseny, posleme ho na login
        if (!Auth::check()) {
            return redirect()
                ->route('login.form')
                ->with('error', 'Please log in to see favorites.');
        }

        $favorites = Favorite::with('product')
            //loading produktov pre zoznam oblubenych
            ->where('user_id', Auth::id())
            ->get();

        return view('favorites.index', compact('favorites'));
    }


     //Добавить товар в избранное.
     //Работает и для обычного POST, и для AJAX (как cart.add).
    public function add(Request $request, Product $product)
    {
        if (!Auth::check()) {
            // ak chce JSON (AJAX), vratime info ze treba login
            if ($request->expectsJson()) {
                return response()->json([
                    'success'           => false,
                    'redirect_to_login' => true,
                    'login_url'         => route('login.form'),
                ]);
            }

            return redirect()
                ->route('login.form')
                ->with('error', 'Please log in to add favorites.');
        }

        $userId = Auth::id();

        $exists = Favorite::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->exists();

        // kontrola, ci uz dany produkt je v oblubenych
        if ($exists) {
            if ($request->expectsJson()) {
                $count = Favorite::where('user_id', $userId)->count();

                return response()->json([
                    'success' => true,
                    'already' => true,
                    'count'   => $count,
                ]);
            }

            return back()->with('info', 'Product is already in favorites.');
        }

        // vytvorenie noveho zaznamu v oblubenych
        Favorite::create([
            'user_id'    => $userId,
            'product_id' => $product->id,
        ]);

        if ($request->expectsJson()) {
            $count = Favorite::where('user_id', $userId)->count();

            return response()->json([
                'success' => true,
                'count'   => $count,
            ]);
        }

        return back()->with('success', 'Product added to favorites.');
    }
    //Удалить товар из избранного
    public function remove(Request $request, Product $product)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false]);
            }

            return redirect()->route('login.form');
        }

        $userId = Auth::id();

        // zmazanie konkretneho produktu z oblubenych
        Favorite::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->delete();

        if ($request->expectsJson()) {
            $count = Favorite::where('user_id', $userId)->count();

            return response()->json([
                'success' => true,
                'count'   => $count,
            ]);
        }

        return back()->with('success', 'Removed from favorites.');
    }

    public function count()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $count = Favorite::where('user_id', Auth::id())->count();

        return response()->json(['count' => $count]);
    }
}
