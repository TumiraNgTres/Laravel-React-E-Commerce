<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'home'])->name('dashboard');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/shop', [ProductController::class, 'shop'])->name('shop');

// cart routes for guest user ---------------------------------------
Route::prefix('cart')
    ->controller(CartController::class)
    ->name('cart.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add/{product}', 'store')->name('store');
        Route::put('/{product}', 'update')->name('update');
        Route::delete('/product', 'destroy')->name('destroy');
    });
// -----------------------------------------------------------------------




// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
