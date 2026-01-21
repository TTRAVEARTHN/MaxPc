@extends('layouts.app')

@section('content')

    <div class="page-container account-page">

        <h1 class="page-title mb-6">My Account</h1>

        {{-- ========================= --}}
        {{-- PERSONAL INFORMATION --}}
        {{-- ========================= --}}
        <div class="panel mb-8">
            <h2 class="panel-title">Personal Information</h2>

            <form method="POST" action="{{ route('account.update') }}" class="form-space">
                @csrf

                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="input">
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" class="input">
                </div>

                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" value="{{ $user->address }}" class="input">
                </div>

                {{-- Role stays unchanged --}}
                <input type="hidden" name="role" value="{{ $user->role }}">

                <button class="btn-primary w-full mt-2">Save Changes</button>
            </form>
        </div>

        {{-- ========================= --}}
        {{-- PASSWORD UPDATE --}}
        {{-- ========================= --}}
        <div class="panel">
            <h2 class="panel-title">Change Password</h2>

            <form method="POST" action="{{ route('account.password') }}" class="form-space">
                @csrf

                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="input" required>
                </div>

                <button class="btn-primary w-full mt-2">Update Password</button>
            </form>
        </div>

        {{-- ========================= --}}
        {{-- MY ORDERS --}}
        {{-- ========================= --}}

{{--        Tento kod bol vytvoreny s pomocou AI.--}}

        <div class="panel mt-8">
            <h2 class="panel-title mb-4">My Orders</h2>

            @if($orders->isEmpty())
                <p class="text-gray-400">
                    You don't have any orders yet.
                </p>
            @else
                <div class="account-orders-wrapper">
                    <table class="table account-orders-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    {{ $order->id }}
                                </td>

                                <td>
                                    {{ $order->created_at->format('Y-m-d') }}
                                </td>

                                <td class="text-gray-300">
                                    @php
                                        $itemsCount = $order->items->sum('quantity');
                                    @endphp
                                    {{ $itemsCount }} item{{ $itemsCount !== 1 ? 's' : '' }}
                                </td>

                                <td>
                                    ${{ number_format($order->total_price, 2) }}
                                </td>

                                <td>
                                    @php $status = $order->status; @endphp
                                    <span class="
                                inline-block px-2 py-1 rounded text-xs
                                @switch($status)
                                    @case('pending')   bg-yellow-900 text-yellow-300 @break
                                    @case('paid')      bg-green-900  text-green-300  @break
                                    @case('shipped')   bg-blue-900   text-blue-300   @break
                                    @case('cancelled') bg-red-900    text-red-300    @break
                                    @default           bg-gray-800   text-gray-300
                                @endswitch">
                                {{ ucfirst($status) }}
                            </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            @endif
        </div>

    </div>

@endsection
