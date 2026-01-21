@extends('layouts.app')

@section('content')

    <div class="auth-container">

        <div class="auth-box">

            <h2 class="auth-title">Create Account</h2>

            {{-- ERROR MESSAGE --}}
            @if($errors->any())
                <div class="alert-error">
                    Please fix the errors below.
                </div>
            @endif

            <form method="POST"
                  action="{{ route('register') }}"
                  class="form-space"
                  id="registerForm">
                @csrf

                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text"
                           name="name"
                           class="input"
                           value="{{ old('name') }}"
                           autocomplete="name"
                           required>
                    <p class="input-error" data-error="name"></p>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           class="input"
                           value="{{ old('email') }}"
                           autocomplete="email"
                           required>
                    <p class="input-error" data-error="email"></p>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password"
                           name="password"
                           class="input"
                           autocomplete="new-password"
                           required>
                    <p class="input-error" data-error="password"></p>
                </div>

                <button type="submit" class="btn-primary w-full">
                    Create Account
                </button>
            </form>

            <p class="auth-switch">
                Already have an account?
                <a href="{{ route('login.form') }}">Login</a>
            </p>

        </div>

    </div>

@endsection
