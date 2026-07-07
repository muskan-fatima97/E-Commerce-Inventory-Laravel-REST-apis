<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticalChartsController;

// Public store routes
Route::get('/', [ProductController::class, 'HomePage'])->name('home');
Route::get('/store', [ProductController::class, 'storePage']);
Route::get('/product/{id}', [ProductController::class, 'productDetailPage']);

// Cart
Route::post('/api/cart', [CartController::class, 'store']);
Route::get('/cart', [CartController::class, 'index']);
Route::patch('/api/cart/{id}', [CartController::class, 'updateQuantity']);
Route::delete('/api/cart/{id}', [CartController::class, 'destroy']);
Route::get('/api/cart/count', [CartController::class, 'count']);

// Checkout / Orders (customer)
Route::get('/checkout', [OrderController::class, 'checkoutPage']);
Route::post('/api/orders', [OrderController::class, 'store']);
Route::get('/order/{id}/confirmation', [OrderController::class, 'confirmation']);

// Admin — protected
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index']);

    Route::get('/inventory', [ProductController::class, 'inventoryPage']);
    Route::get('/products/create', [ProductController::class, 'create']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/product/{id}/edit', [ProductController::class, 'edit']);
    Route::put('/api/product/{id}', [ProductController::class, 'update']);
    Route::delete('/api/product/{id}', [ProductController::class, 'destroy']);

    Route::get('/admin/orders', [OrderController::class, 'index']);
    Route::get('/admin/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/api/orders/{id}/status', [OrderController::class, 'updateStatus']);

    Route::get('/api/admin/analytics/revenue', [AnalyticalChartsController::class, 'revenue']);
    Route::get('/api/admin/analytics/orders-by-status', [AnalyticalChartsController::class, 'ordersByStatus']);
    Route::get('/api/admin/analytics/top-products', [AnalyticalChartsController::class, 'topProducts']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    
});
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'myOrders']);
});

require __DIR__.'/settings.php';