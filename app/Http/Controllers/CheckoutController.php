<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form')
                ->with('error', 'You must login to proceed.');
        }

        // TODO: move cart items into orders

        return redirect()->route('home')
            ->with('success', 'Order placed successfully!');
    }
}
