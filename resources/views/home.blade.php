@extends('layouts.app')

@section('content')

    {{-- =============================== --}}
    {{-- HERO SECTION --}}
    {{-- =============================== --}}
    <section class="hero-section">
        <div class="hero-inner">
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
    <section class="stats-bg stats-section">
        <div class="stats-grid">

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
    <section class="cta-section">

        <h2 class="cta-title">
            Ready to Get Started?
        </h2>

        <p class="cta-subtitle">
            Browse our catalog of gaming PCs, workstations, and peripherals.
            Every system comes with a 2-year warranty and free technical support.
        </p>

        <a href="{{ route('catalog.index') }}" class="blue-btn">
            View Full Catalog
        </a>

    </section>

@endsection
