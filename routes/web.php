<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KuitansiController;
use App\Http\Controllers\TagihanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/kuitansi/{pembayaran}', [KuitansiController::class, 'cetak'])
        ->name('kuitansi.pdf');
});

Route::get('/kuitansi/{pembayaran}/pdf', [KuitansiController::class, 'pdf'])
     ->name('kuitansi.pdf');

Route::get('/tagihan/{tagihan}/pdf', [TagihanController::class, 'pdf'])
     ->name('tagihan.pdf');

Route::get('/', function () {
    return view('landing');
});

// Atau jika halaman lain sudah ada di '/', pakai URL berbeda:
Route::get('/landing', function () {
    return view('landing');
});

require __DIR__.'/auth.php';
