@extends('layouts.app')

@section('content')

    {{-- =============================== --}}
    {{-- HERO SECTION --}}
    {{-- =============================== --}}
    <section class="hero-section">
        <div class="max-w-7xl mx-auto px-8 relative z-10">

            <h1 class="hero-title mb-6">
                Buy Your Dream PC
            </h1>

            <p class="hero-subtitle mb-8">
                Discover high-performance gaming rigs and professional workstations
                built with premium components.
            </p>

            <a href="{{ route('catalog.index') }}" class="blue-btn">
                Choose from Catalog
            </a>

        </div>
    </section>



    {{-- =============================== --}}
    {{-- STATS SECTION --}}
    {{-- =============================== --}}
    <section class="stats-bg py-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 text-center gap-10">

            <div>
                <div class="stats-icon mb-2">👥</div>
                <div class="stats-number">10,000+</div>
                <div class="stats-label">PCs Sold</div>
            </div>

            <div>
                <div class="stats-icon mb-2">🏅</div>
                <div class="stats-number">98%</div>
                <div class="stats-label">Customer Satisfaction</div>
            </div>

            <div>
                <div class="stats-icon mb-2">📈</div>
                <div class="stats-number">5 Years</div>
                <div class="stats-label">In Business</div>
            </div>

            <div>
                <div class="stats-icon mb-2">⭐</div>
                <div class="stats-number">4.9/5</div>
                <div class="stats-label">Average Rating</div>
            </div>

        </div>
    </section>



    {{-- =============================== --}}
    {{-- CALL TO ACTION --}}
    {{-- =============================== --}}
    <section class="py-28 text-center">

        <h2 class="text-2xl text-white font-semibold mb-4">
            Ready to Get Started?
        </h2>

        <p class="text-gray-400 max-w-2xl mx-auto mb-10">
            Browse our catalog of gaming PCs, workstations, and peripherals.
            Every system comes with a 2-year warranty and free technical support.
        </p>

        <a href="{{ route('catalog.index') }}" class="blue-btn">
            View Full Catalog
        </a>

    </section>

@endsection
