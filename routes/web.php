<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\PiutangController;

// Redirect root URL to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('produk.index') : redirect()->route('login');
})->name('home');

// Authentication routes (Laravel Breeze)
require __DIR__ . '/auth.php';

// Middleware for authenticated users
Route::middleware(['auth'])->group(function () {

    // Admin Dashboard (optional if needed)
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    // Produk Routes
    Route::resource('produk', ProdukController::class);

    // Penjualan Routes
    Route::resource('penjualan', PenjualanController::class);
    Route::get('/penjualan/{penjualan}', [PenjualanController::class, 'show'])->name('penjualan.show');

    // Pembelian Routes
    Route::resource('pembelian', PembelianController::class);
    Route::get('/pembelian/{id}', [PembelianController::class, 'show'])->name('pembelian.show');

    // Hutang Routes
    Route::resource('hutang', HutangController::class);
    Route::post('/hutang/bayar/{id}', [HutangController::class, 'bayarHutang'])->name('hutang.bayar');

    // Piutang Routes
    Route::resource('piutang', PiutangController::class);
    Route::post('/piutang/bayar/{id}', [PiutangController::class, 'bayarPiutang'])->name('piutang.bayar');
});

// Fallback route for undefined or unauthorized access
Route::fallback(function () {
    return redirect('/login')->with('error', 'Halaman yang Anda cari tidak tersedia.');
});
