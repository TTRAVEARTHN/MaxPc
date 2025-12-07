@extends('layouts.app')

@section('content')

    <div class="page-container">

        <h1 class="page-title">Orders</h1>

        @if($orders->isEmpty())
            <p class="text-gray-400">No orders found.</p>
        @else

            <div class="card-box">

                <table class="table">
                    <thead>
                    <tr>
                        <th>Order #</th>
                        <th>User</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width: 90px;">Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($orders as $order)
                        <tr>

                            {{-- ID --}}
                            <td>{{ $order->id }}</td>

                            {{-- USER --}}
                            <td>{{ $order->user->name }}</td>

                            {{-- ITEMS --}}
                            <td class="text-gray-300">
                                @foreach($order->items as $item)
                                    <div class="text-sm">
                                        {{ $item->product->name }}
                                        <span class="text-gray-400">x{{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            </td>

                            {{-- TOTAL --}}
                            <td>${{ number_format($order->total_price, 2) }}</td>

                            {{-- STATUS --}}
                            <td>
                                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status"
                                            class="select"
                                            onchange="this.form.submit()">

                                        <option value="pending"   @selected($order->status=='pending')>Pending</option>
                                        <option value="paid"      @selected($order->status=='paid')>Paid</option>
                                        <option value="shipped"   @selected($order->status=='shipped')>Shipped</option>
                                        <option value="cancelled" @selected($order->status=='cancelled')>Cancelled</option>
                                    </select>
                                </form>
                            </td>

                            {{-- DATE --}}
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>

                            {{-- DELETE --}}
                            <td>
                                <form action="{{ route('admin.orders.delete', $order->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this order?');">
                                    @csrf
                                    @method('DELETE')

                                    <button class="text-red-400 hover:text-red-500">
                                        Delete
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>

                </table>

            </div>

        @endif

    </div>

@endsection
