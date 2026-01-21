<?php
//namespace App\Http\Controllers;
//
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
//use App\Models\Cart;
//
//class CheckoutController extends Controller
//{
//    public function process(Request $request)
//    {
//        // kontrola prihlasenia
//        if (!Auth::check()) {
//            return redirect()
//                ->route('login.form')
//                ->with('error', 'You must log in to proceed to checkout.');
//        }
//
//        $user = Auth::user();
//
//        // kontrola adresy
//        if (!$user->address) {
//            return redirect()
//                ->route('account') // или твой route на профиль
//                ->with('error', 'Please add your shipping address in profile before placing an order.');
//        }
//
//        // najdeme kosik usera
//        $cart = Cart::with('items.product')
//            ->where('user_id', $user->id)
//            ->first();
//
//        // ak je kosik prazdny
//        if (!$cart || $cart->items->isEmpty()) {
//            return redirect()
//                ->route('cart.index')
//                ->with('error', 'Your cart is empty.');
//        }
//
//        return redirect()
//            ->route('home')
//            ->with('success', 'Order placed successfully!');
//    }
//}
