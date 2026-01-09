<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Halaman Utama (Public)
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. Auth Google (Public)
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// 3. Rute yang butuh Login (Auth)
Route::middleware('auth')->group(function () {

    // Profile User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Keranjang Belanja (Cart) - SEMUA MASUK PREFIX CART
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add/{id}', [CartController::class, 'addToCart'])->name('add');
        Route::post('/update/{id}', [CartController::class, 'update'])->name('update');

        // SINKRONISASI: Pakai DELETE agar sesuai dengan standar form di view
        Route::delete('/destroy/{id}', [CartController::class, 'destroy'])->name('destroy');

        // Route untuk mengosongkan keranjang setelah bayar sukses
        Route::get('/clear', function() {
            \App\Models\Cart::where('user_id', auth()->id())->delete();
            return redirect()->route('home')->with('success', 'Pembayaran Berhasil!');
        })->name('clear');
    });

    // Fitur Pro: Checkout & Orders
    Route::get('/buy-now/{id}', [CartController::class, 'buyNow'])->name('buy.now');
    Route::post('/checkout', [CartController::class, 'checkoutSelected'])->name('checkout.selected');
    Route::post('/order/process', [CartController::class, 'processOrder'])->name('order.process');
    Route::get('/my-orders', [CartController::class, 'ordersIndex'])->name('orders.index');

    // Payment Gateway (Midtrans)
    Route::post('/get-snap-token', [PaymentController::class, 'getSnapToken'])->name('payment.token');
});

require __DIR__.'/auth.php';
