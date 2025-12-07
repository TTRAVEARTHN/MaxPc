@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto py-10 text-white px-6">

        <h1 class="page-title mb-6">My Account</h1>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

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

    </div>

@endsection
