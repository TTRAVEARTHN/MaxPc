<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    /**
     * Show all orders.
     */
    public function index(Request $request)
    {
        $orders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders.orders', compact('orders'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,shipped,cancelled'
        ]);

        $order = Order::findOrFail($orderId);
        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Order status updated.');
    }

    /**
     * Delete an order
     */
    public function delete($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->items()->delete();
        $order->delete();

        return back()->with('success', 'Order deleted.');
    }
}

