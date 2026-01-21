<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        // eager loading usera a poloziek aby sa zamedzilo N+1
        $orders = Order::with(['user', 'items.product'])
            // eager loading usera a poloziek aby sa zamedzilo N+1
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders.orders', compact('orders'));
    }

    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            // kontrola a whitelist statusov
            'status' => 'required|string|in:pending,paid,shipped,cancelled'
        ]);

        $order = Order::findOrFail($orderId);
        $order->status = $request->status;
        $order->save();

        // flash sprava pre admina
        return back()->with('success', 'Order status updated.');
    }

    public function delete($orderId)
    {
        $order = Order::findOrFail($orderId);
        // mazeme polozky objednavky aby nezostali siroty v DB
        $order->items()->delete();
        // mazeme samotnu objednavku
        $order->delete();

        return back()->with('success', 'Order deleted.');
    }
}

