<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

// Account area
Route::middleware('auth')->group(function () {

    Route::get('/account', [AccountController::class, 'index'])->name('account');

    Route::post('/account/update', [AccountController::class, 'update'])->name('account.update');

    Route::post('/account/password', [AccountController::class, 'updatePassword'])->name('account.password');

});

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



Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout/place', [OrderController::class, 'placeOrder'])->name('checkout.place');


