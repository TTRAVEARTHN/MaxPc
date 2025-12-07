<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Show user favorites
     */
    public function index()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->with('product')
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Add product to favorites
     */
    public function store($productId)
    {
        $userId = Auth::id();

        // prevent duplicate favorites
        $exists = Favorite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return back()->with('info', 'Product is already in favorites.');
        }

        Favorite::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return back()->with('success', 'Added to favorites.');
    }

    /**
     * Remove favorite
     */
    public function destroy($productId)
    {
        Favorite::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();

        return back()->with('success', 'Removed from favorites.');
    }
}


