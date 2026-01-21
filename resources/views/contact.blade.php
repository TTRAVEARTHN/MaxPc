@extends('layouts.app')

@section('content')

    <div class="page-container contact-page">

        <h1 class="page-title mb-6">Contact Us</h1>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- VALIDATION ERRORS --}}
        @if($errors->any())
            <div class="alert-error mb-4">
                Please fix the errors below.
            </div>
        @endif

        <div class="panel">
            <h2 class="panel-title mb-4">Send us a message</h2>

            <form method="POST"
                  action="{{ route('contact.send') }}"
                  class="form-space">
                @csrf

                {{-- NAME --}}
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           class="input @error('name') error @enderror"
                           required>
                    @error('name')
                    <p class="input-error">{{ $message }}</p>
                    @else
                        <p class="input-error"></p>
                        @enderror
                </div>

                {{-- EMAIL --}}
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="input @error('email') error @enderror"
                           required>
                    @error('email')
                    <p class="input-error">{{ $message }}</p>
                    @else
                        <p class="input-error"></p>
                        @enderror
                </div>

                {{-- MESSAGE --}}
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea name="message"
                              rows="5"
                              class="input @error('message') error @enderror"
                              required>{{ old('message') }}</textarea>
                    @error('message')
                    <p class="input-error">{{ $message }}</p>
                    @else
                        <p class="input-error"></p>
                        @enderror
                </div>

                <button class="btn-primary w-full mt-2">
                    Send Message
                </button>
            </form>
        </div>

    </div>

@endsection
