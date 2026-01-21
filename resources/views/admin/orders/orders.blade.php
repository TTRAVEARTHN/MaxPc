@extends('layouts.app')

@section('content')

    <div class="page-container">

        {{-- HEADER + search --}}
        <div class="mb-6">
            <h1 class="page-title mb-4">Orders</h1>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                {{-- SEARCH FORM (id, name, email) --}}
                <form method="GET"
                      action="{{ route('admin.orders') }}"
                      class="flex w-full sm:w-auto gap-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? request('search') }}"
                        placeholder="Search by ID, user name or email..."
                        class="input flex-1 min-w-0"
                    >
                    <button class="gray-btn px-4 py-2 whitespace-nowrap">
                        Search
                    </button>
                </form>

            </div>
        </div>

        @if($orders->isEmpty())
            <p class="text-gray-400">No orders found.</p>
        @else

            <div class="card-box overflow-x-auto">

                <table class="table min-w-[800px]">
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
                            <td>
                                <div class="font-semibold">{{ $order->user->name }}</div>
                                <div class="text-xs text-gray-400">
                                    {{ $order->user->email }}
                                </div>
                            </td>

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
                                <form method="POST"
                                      action="{{ route('admin.orders.updateStatus', $order->id) }}">
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

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>

            </div>

        @endif

    </div>

@endsection
