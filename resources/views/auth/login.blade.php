@extends('layouts.app')

@section('content')

    <div class="auth-container">

        <div class="auth-box">

            <h2 class="auth-title">Login</h2>

            {{-- ERROR MESSAGE --}}
            @if(session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif

            <form method="POST"
                  action="{{ route('login') }}"
                  class="form-space"
                  id="loginForm">
                @csrf

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="input"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                    >
                    <p class="input-error" data-error="email"></p>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="input"
                        autocomplete="current-password"
                        required
                    >
                    <p class="input-error" data-error="password"></p>
                </div>

                <button type="submit" class="btn-primary w-full">
                    Login
                </button>
            </form>

            <p class="auth-switch">
                Don’t have an account?
                <a href="{{ route('register.form') }}">Register</a>
            </p>

        </div>

    </div>

@endsection
