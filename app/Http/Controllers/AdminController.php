<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function products()
    {
        $products = Product::all();
        return view('admin.products.products', compact('products'));
    }

    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function orders()
    {
        $orders = Order::with('user')->get();
        return view('admin.orders', compact('orders'));
    }

}

