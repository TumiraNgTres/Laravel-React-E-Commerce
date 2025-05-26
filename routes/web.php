<?php

use App\Enum\RolesEnum;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;


/* ------ Guest Routes ---------------- */

Route::get('/', [ProductController::class, 'home'])->name('dashboard');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/shop', [ProductController::class, 'shop'])->name('shop');

Route::get('/s/{vendor:store_name}', [VendorController::class, 'profile'])->name('vendor.profile');

// cart routes for guest user ---------------------------------------
Route::prefix('cart')
    ->controller(CartController::class)
    ->name('cart.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add/{product}', 'store')->name('store');
        Route::put('/{product}', 'update')->name('update');
        Route::delete('/{product}', 'destroy')->name('destroy');
    });
/* ----------------------------------------------------------------------- */

Route::post('stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


/* ----------- Auth Routes -------------------------------------------------------- */
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // verified logined user routes --------------------------------------------------

    Route::middleware(['verified'])->group(function () {

        Route::post('cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

        // stripe routes
        Route::prefix('stripe')
            ->controller(StripeController::class)
            ->name('stripe.')
            ->group(function () {
                Route::get('success',  'success')->name('success');
                Route::get('failure',  'failure')->name('failure');
                Route::post('connect',  'connect')->name('connect')->middleware(['role:' . RolesEnum::Vendor->value]);
            });

        // become a vendor
        Route::post('become-a-vendor', [VendorController::class, 'store'])->name('vendor.store');
    });

    // -----------------------------------------------------------------------------
});

/* ----------------------------------------- */

require __DIR__ . '/auth.php';
