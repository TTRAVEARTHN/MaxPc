<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FavoriteController;

Route::get('/', function () {
    return view('home');
})->name('home');



// CONTACT
Route::get('/contact', [ContactController::class, 'show'])
    ->name('contact');

Route::post('/contact', [ContactController::class, 'send'])
    ->name('contact.send');


// Account area
Route::middleware('auth')->group(function () {

    Route::get('/account', [AccountController::class, 'index'])->name('account');

    Route::post('/account/update', [AccountController::class, 'update'])->name('account.update');

    Route::post('/account/password', [AccountController::class, 'updatePassword'])->name('account.password');

});

Route::get('/cart/count', [CartController::class, 'count'])
    ->name('cart.count');
Route::get('/cart/count', function() {
    if (!auth()->check()) {
        return response()->json(['count' => 0]);
    }

    $cart = auth()->user()->cart;

    $count = $cart
        ? $cart->items()->sum('quantity')
        : 0;

    return response()->json(['count' => $count]);
});

Route::get('/compare/count', [CompareController::class, 'count'])
    ->name('compare.count');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', function () {
        return 'Admin Dashboard';
    })->name('admin.dashboard');
});

/* =========================
   ADMIN PANEL
   ========================= */
Route::middleware(['auth', 'admin'])->group(function () {

    // USERS
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::patch('/admin/users/{id}/promote', [AdminUserController::class, 'promote'])
        ->name('admin.users.promote');
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'delete'])
        ->name('admin.users.delete');

    // ORDERS
    Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders');
    Route::patch('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])
        ->name('admin.orders.updateStatus');
    Route::delete('/admin/orders/{id}', [AdminOrderController::class, 'delete'])
        ->name('admin.orders.delete');

    Route::get('/admin/products', [AdminProductController::class, 'index'])
        ->name('admin.products');

    Route::get('/admin/products/create', [AdminProductController::class, 'create'])
        ->name('admin.products.create');

    Route::post('/admin/products', [AdminProductController::class, 'store'])
        ->name('admin.products.store');

    Route::get('/admin/products/{product}/edit', [AdminProductController::class, 'edit'])
        ->name('admin.products.edit');

    Route::patch('/admin/products/{product}', [AdminProductController::class, 'update'])
        ->name('admin.products.update');

    Route::delete('/admin/products/{product}', [AdminProductController::class, 'delete'])
        ->name('admin.products.delete');

});


// Каталог товаров (для всех)
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog.index');

// Просмотр товара
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

Route::post('/cart/add/{product}', [CartController::class, 'add'])
    ->name('cart.add')
    ->middleware('auth');



// AUTH PAGES (views)
Route::get('/login', function () {
    return view('auth.login');
})->name('login.form');

Route::get('/register', function () {
    return view('auth.register');
})->name('register.form');

// AUTH ACTIONS (controllers)
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// CART ROUTES
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');

Route::patch('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');

Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');

Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout');

// COMPARE
Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
Route::post('/compare/add/{product}', [CompareController::class, 'add'])->name('compare.add');
Route::delete('/compare/remove/{product}', [CompareController::class, 'remove'])->name('compare.remove');
Route::delete('/compare/clear', [CompareController::class, 'clear'])->name('compare.clear');


Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout/place', [OrderController::class, 'placeOrder'])->name('checkout.place');

Route::get('/favorites', [FavoriteController::class, 'index'])
    ->name('favorites.index');

Route::post('/favorites/add/{product}', [FavoriteController::class, 'add'])
    ->name('favorites.add');

Route::delete('/favorites/remove/{product}', [FavoriteController::class, 'remove'])
    ->name('favorites.remove');

Route::get('/favorites/count', [FavoriteController::class, 'count'])
    ->name('favorites.count');


