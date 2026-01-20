<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC Store</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>

<body class="bg-dark text-gray-200">

{{-- =========================== --}}
{{-- HEADER / NAVIGATION --}}
{{-- =========================== --}}
<header class="w-full bg-nav border-b border-gray-800 sticky top-0 z-50">
    <nav class="max-w-7xl mx-auto px-8 py-4 flex items-center justify-between">

        {{-- LEFT SIDE --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="text-xl font-semibold flex items-center gap-2">
                <span>MaxPC</span>
            </a>

            {{-- DESKTOP MENU --}}
            <ul class="hidden md:flex gap-6 ml-10">
                <li>
                    <a class="nav-link {{ request()->routeIs('home') ? 'text-white' : '' }}"
                       href="{{ route('home') }}">
                        Home
                    </a>
                </li>

                <li>
                    <a class="nav-link {{ request()->routeIs('catalog.index') ? 'text-white' : '' }}"
                       href="{{ route('catalog.index') }}">
                        Catalog
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('contact') ? 'text-white' : '' }}"
                       href="{{ route('contact') }}">
                        Contact
                    </a>
                </li>
            </ul>
        </div>

        @php
            use App\Models\Cart;
            use App\Models\Favorite;

            // compare из сессии
            $compareIds   = session('compare', []);
            $compareCount = is_array($compareIds) ? count($compareIds) : 0;

            $cartCount      = 0;
            $favoritesCount = 0;

            if (auth()->check()) {
                $user = auth()->user();

                // корзина
                $cart = Cart::with('items')->where('user_id', $user->id)->first();
                if ($cart) {
                    $cartCount = $cart->items->sum('quantity');
                }

                // избранное
                $favoritesCount = Favorite::where('user_id', $user->id)->count();
            }
        @endphp

        {{-- RIGHT SIDE (desktop only) --}}
        <div class="hidden md:flex items-center gap-3">

            {{-- FAVORITES --}}
            <a href="{{ route('favorites.index') }}" class="nav-btn relative">
                Favorites
                <span id="favoritesCount"
                      class="ml-1 px-2 py-0.5 text-xs rounded bg-blue-500
                             {{ $favoritesCount > 0 ? '' : 'hidden' }}">
                    {{ $favoritesCount }}
                </span>
            </a>

            {{-- COMPARE --}}
            <a href="{{ route('compare.index') }}" class="nav-btn relative">
                Compare
                <span id="compareCount"
                      class="ml-1 px-2 py-0.5 text-xs rounded bg-blue-500
                             {{ $compareCount > 0 ? '' : 'hidden' }}">
                    {{ $compareCount }}
                </span>
            </a>

            {{-- ACCOUNT DROPDOWN --}}
            <div class="relative">
                @auth
                    <div class="relative hidden md:block">

                        <div id="account-dropdown" class="relative">

                            <button id="account-btn" class="nav-btn">Account</button>

                            <div id="account-menu"
                                 class="hidden absolute right-0 mt-2 w-48 bg-[#1f2635] rounded shadow-lg py-2 z-50">

                                <a href="{{ route('account') }}" class="dropdown-link block px-4 py-2">Profile</a>

                                @if(Auth::user()->role === 'admin')
                                    <div class="border-t border-gray-700 my-2"></div>
                                    <a href="{{ route('admin.products') }}" class="dropdown-link block px-4 py-2">
                                        Manage Products
                                    </a>
                                    <a href="{{ route('admin.orders') }}" class="dropdown-link block px-4 py-2">
                                        Manage Orders
                                    </a>
                                    <a href="{{ route('admin.users') }}" class="dropdown-link block px-4 py-2">
                                        Users
                                    </a>
                                @endif

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-link w-full text-left px-4 py-2">Logout</button>
                                </form>

                            </div>

                        </div>

                    </div>

                @endauth

                @guest
                    <a href="{{ route('login.form') }}" class="nav-btn">Login</a>
                    <a href="{{ route('register.form') }}" class="nav-btn">Register</a>
                @endguest
            </div>

            {{-- CART --}}
            <a href="{{ route('cart.index') }}" class="nav-btn relative">
                Cart
                <span id="cartCount"
                      class="ml-1 text-xs bg-blue-500 px-1.5 py-0.5 rounded {{ $cartCount ? '' : 'hidden' }}">
                    {{ $cartCount }}
                </span>
            </a>
        </div>

        {{-- HAMBURGER (only mobile) --}}
        <button id="burger"
                class="md:hidden text-2xl px-3 py-2 rounded bg-[#1f2330]">
            ☰
        </button>

    </nav>

    {{-- MOBILE MENU --}}
    <div id="mobile-menu" class="hidden md:hidden bg-[#1f2635] border-t border-gray-800">
        <ul class="flex flex-col py-4">

            <li><a href="{{ route('home') }}" class="mobile-link">Home</a></li>
            <li><a href="{{ route('catalog.index') }}" class="mobile-link">Catalog</a></li>
            <li><a href="{{ route('contact') }}" class="mobile-link">Contact</a></li>

            <li class="border-t border-gray-700 my-2"></li>

            {{-- FAVORITES + COMPARE + CART c теми же счётчиками --}}
            <li>
                <a href="{{ route('favorites.index') }}" class="mobile-link flex items-center justify-between">
                    <span>Favorites</span>
                    @if($favoritesCount > 0)
                        <span class="ml-2 text-xs bg-blue-500 px-1.5 py-0.5 rounded">
                            {{ $favoritesCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('compare.index') }}" class="mobile-link flex items-center justify-between">
                    <span>Compare</span>
                    @if($compareCount > 0)
                        <span class="ml-2 text-xs bg-blue-500 px-1.5 py-0.5 rounded">
                            {{ $compareCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('cart.index') }}" class="mobile-link flex items-center justify-between">
                    <span>Cart</span>
                    @if($cartCount > 0)
                        <span class="ml-2 text-xs bg-blue-500 px-1.5 py-0.5 rounded">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li class="border-t border-gray-700 my-2"></li>

            @auth
                <li><a href="{{ route('account') }}" class="mobile-link">Account</a></li>

                @if(Auth::user()->role === 'admin')
                    <li><a href="{{ route('admin.products') }}" class="mobile-link">Manage Products</a></li>
                    <li><a href="{{ route('admin.orders') }}" class="mobile-link">Manage Orders</a></li>
                    <li><a href="{{ route('admin.users') }}" class="mobile-link">Users</a></li>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="mobile-link w-full text-left">Logout</button>
                </form>
            @endauth

            @guest
                <li><a href="{{ route('login.form') }}" class="mobile-link">Login</a></li>
                <li><a href="{{ route('register.form') }}" class="mobile-link">Register</a></li>
            @endguest

        </ul>
    </div>
</header>



{{-- =========================== --}}
{{-- MAIN CONTENT --}}
{{-- =========================== --}}
<main>
    @yield('content')
</main>


{{-- =========================== --}}
{{-- FOOTER --}}
{{-- =========================== --}}
<footer class="footer-bg border-t border-gray-800 mt-20 py-14">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 px-8">

        {{-- ABOUT --}}
        <div>
            <h3 class="text-xl font-semibold flex items-center gap-2 mb-3">
                MaxPC
            </h3>
            <p class="text-gray-400">
                Your trusted destination for high-performance gaming and work computers.
            </p>
        </div>

        {{-- CONTACT --}}
        <div>
            <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
            <p class="text-gray-400">Zilina, ZA 010 01</p>
            <p class="text-gray-300 mt-3"> +1234556 6241</p>
            <p class="text-gray-300">✉️ info@pcstore.com</p>
        </div>

        {{-- MAP --}}
        <div>
            <h3 class="text-lg font-semibold mb-4">Find Us</h3>

            <a href="https://shorturl.at/ek52q"
               class="blue-btn inline-block"
               target="_blank"
               rel="noopener noreferrer">
                Open in Google Maps
            </a>

            <p class="text-gray-500 mt-3">
                Visit our showroom to see our products in person.
            </p>
        </div>

    </div>

    <p class="text-center text-gray-600 mt-10">
        © 2025 MaxPc. All rights reserved.
    </p>
</footer>

</body>
</html>
