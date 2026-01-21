<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $orders = Order::with(['user', 'items.product'])
            ->when($search, function ($q) use ($search) {

                $q->where(function ($inner) use ($search) {


                    if (is_numeric($search)) {
                        $inner->where('id', (int)$search);
                    }


                    $inner->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });


                    $inner->orWhereHas('items.product', function ($pq) use ($search) {
                        $pq->where('name', 'LIKE', "%{$search}%");
                    });

                });

            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.orders.orders', [
            'orders' => $orders,
            'search' => $search,
        ]);
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

        $order->delete();

        return back()->with('success', 'Order deleted.');
    }
}

