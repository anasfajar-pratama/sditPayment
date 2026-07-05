<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KuitansiController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\TagihanPublicController;
use App\Http\Controllers\KasHarianPrintController;
use App\Http\Controllers\KaryawanPdfController;
use App\Http\Controllers\SlipGajiController;
use App\Http\Controllers\OperasionalController;


Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Route::get('/kuitansi/{pembayaran}', [KuitansiController::class, 'cetak'])
    //     ->name('kuitansi.pdf');
    Route::get('/kas-harian/print', KasHarianPrintController::class)
         ->name('kas-harian.print');

    // Data Karyawan — cetak PDF (dengan filter job/status/kepeg/search)
    Route::get('/karyawan/pdf', [KaryawanPdfController::class, 'print'])
         ->name('karyawan.pdf');

    Route::get('/kuitansi/{pembayaran}', [KuitansiController::class, 'cetak'])
    ->name('kuitansi.cetak');

    Route::get('/slip-gaji/pdf', [SlipGajiController::class, 'cetak'])
        ->name('slip-gaji.pdf');

    Route::get('/operasional/pdf', [OperasionalController::class, 'cetakPdf'])
        ->name('operasional.pdf');

    Route::get('/sosial/pdf', [OperasionalController::class, 'sosialPdf'])
        ->name('sosial.pdf');

    Route::get('/upah/pdf', [OperasionalController::class, 'upahPdf'])
        ->name('upah.pdf');
});

Route::get('/slip-gaji/share/{karyawanId}/{bulan}/{tahun}', [SlipGajiController::class, 'share'])
    ->name('slip-gaji.share');

Route::get('/kuitansi/{pembayaran}/pdf', [KuitansiController::class, 'pdf'])
     ->name('kuitansi.pdf');
Route::get('/k/{token}', function (string $token) {
    $link = \DB::table('pdf_links')
            ->where('token', $token)
            ->where('jenis', 'kuitansi')
            ->where('expired_at', '>', now())
            ->first();
    abort_if(!$link, 404);
        
    return redirect($link->original_url . '?_internal=1&_token='.$token);
})->name('pdf.kuitansi');


Route::get('/tagihan/{tagihan}/pdf', [TagihanController::class, 'pdf'])
     ->name('tagihan.pdf');

// Route::get('/', function () {
//     return view('landing');
// });

// Atau jika halaman lain sudah ada di '/', pakai URL berbeda:
Route::get('/landing', function () {
    return view('landing');
});

// ── Route publik: bisa diakses tanpa login oleh wali murid ───────────────────
// Token = hasil encrypt(id) dengan karakter +/ diganti -_
// Contoh URL: /tagihan/share/eyJpdiI6Ik...
Route::get('/tagihan/share/{token}', [TagihanPublicController::class, 'show'])
    ->name('tagihan.public.share');

// ── Route export CSV: hanya untuk user yang sudah login ──────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/tagihan/export', [TagihanPublicController::class, 'exportCsv'])
        ->name('tagihan.export.csv');

    Route::get('/calon-siswa/template', function () {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CalonSiswaTemplateExport(),
            'template-import-calon-siswa.xlsx'
        );
    })->name('calon-siswa.template');
});

require __DIR__.'/auth.php';
