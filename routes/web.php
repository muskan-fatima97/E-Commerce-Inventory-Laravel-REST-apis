<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Route::inertia('/', 'Welcome')->name('home');
Route::get('/', [ProductController::class, 'HomePage'])->name('home');  
Route::get('/inventory', [ProductController::class, 'inventoryPage']);
Route::get('/products/create', [ProductController::class, 'create']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/store', [ProductController::class, 'storePage']);
Route::get('/product/{id}',[ProductController::class, 'productDetailPage']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
