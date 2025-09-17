<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\MutasiController;

Route::get('/', function () {
    return view('dashboard', ['title' => 'Dashboard']);
})->name('dashboard');

Route::resource('barang', BarangController::class);
Route::get('barang/{id}/qr', [BarangController::class, 'qr'])->name('barang.qr');

Route::view('/laporan', 'laporan', ['title' => 'Laporan'])->name('laporan');
 Route::get('/mutasi', [MutasiController::class, 'index'])->name('mutasi.index');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
