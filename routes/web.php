<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\MutasiBarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\MaintenanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard', ['title' => 'Dashboaard']);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('barang', BarangController::class);
    Route::get('barang/{id}/qr', [BarangController::class, 'qr'])->name('barang.qr');
    Route::middleware('auth')->group(function () {
    Route::get('/barangs/{barang}/mutasi', [MutasiBarangController::class, 'create'])->name('mutasi.create');
    Route::post('/barangs/{barang}/mutasi', [MutasiBarangController::class, 'store'])->name('mutasi.store');
    Route::get('/mutasi', [MutasiBarangController::class, 'index'])->name('mutasi.index');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/ruangan',        [RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('/ruangan/print',  [RuanganController::class, 'print'])->name('ruangan.print');

    Route::get('/maintenance',                [MaintenanceController::class, 'index'])->name('maintenance.index');

    // Buat pemeliharaan untuk barang tertentu
    Route::get('/barangs/{barang}/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/barangs/{barang}/maintenance',       [MaintenanceController::class, 'store'])->name('maintenance.store');

    // Edit/Update
    Route::get('/maintenance/{maintenance}/edit',      [MaintenanceController::class, 'edit'])->name('maintenance.edit');
    Route::put('/maintenance/{maintenance}',           [MaintenanceController::class, 'update'])->name('maintenance.update');

    // Approve/Reject (admin)
    Route::middleware('role:admin')->group(function () {
        Route::post('/maintenance/{maintenance}/approve', [MaintenanceController::class,'approve'])->name('maintenance.approve');
        Route::post('/maintenance/{maintenance}/reject',  [MaintenanceController::class,'reject'])->name('maintenance.reject');
        Route::delete('/maintenance/{maintenance}',        [MaintenanceController::class,'destroy'])->name('maintenance.destroy');
    });


});

require __DIR__.'/auth.php';
