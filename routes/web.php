<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\MutasiBarangController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;

// untuk filter "read all" notifikasi mutasi
use App\Notifications\MutasiRequestedNotification;
use App\Notifications\MutasiRequestResolvedNotification;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/dashboard');


/* ============= Hanya untuk user login + verified ============= */
Route::middleware(['auth', 'verified'])->group(function () {

    /* ---------------- Dashboard ---------------- */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /* ---------------- Profile ------------------ */
    Route::get('/profile',  [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::put('/profile/update-all', [ProfileController::class, 'updateAll'])
        ->name('profile.update.all');

    /* ---------------- Barang ------------------- */
    Route::resource('barang', BarangController::class);

    Route::get('/barang/{barang}/qr', [BarangController::class, 'qr'])
        ->name('barang.qr');

    Route::get('/barang/export', [BarangController::class, 'export'])
        ->name('barang.export');

    /* ---------------- Mutasi Barang ------------ */

    // Form mutasi / ajukan mutasi untuk satu barang
    Route::get('/barang/{barang}/mutasi', [MutasiBarangController::class, 'create'])
        ->name('mutasi.create');

    // ADMIN: mutasi langsung (ubah lokasi barang)
    Route::post('/barang/{barang}/mutasi', [MutasiBarangController::class, 'store'])
        // kalau sudah punya middleware role admin, bisa aktifkan:
        // ->middleware('role:admin')
        ->name('mutasi.store');

    // PENGELOLA: ajukan mutasi (kirim notifikasi ke admin)
    Route::post('/barang/{barang}/mutasi-request', [MutasiBarangController::class, 'requestMutasi'])
        ->name('mutasi.request');

    // Daftar riwayat mutasi (semua barang)
    Route::get('/mutasi', [MutasiBarangController::class, 'index'])
        ->name('mutasi.index');

    // ADMIN: setujui / tolak permintaan mutasi dari notifikasi
    Route::middleware('role:admin')->group(function () {
        Route::post('/notifications/mutasi/{notificationId}/approve',
            [MutasiBarangController::class, 'approveRequest']
        )->name('mutasi.request.approve');

        Route::post('/notifications/mutasi/{notificationId}/reject',
            [MutasiBarangController::class, 'rejectRequest']
        )->name('mutasi.request.reject');
    });

    /* ---------------- Ruangan ------------------ */
    Route::get('/ruangan', [RuanganController::class, 'index'])
        ->name('ruangan.index');

    Route::get('/ruangan/print', [RuanganController::class, 'print'])
        ->name('ruangan.print');

    Route::get('/ruangan/{location}', [\App\Http\Controllers\RuanganController::class, 'show'])
        ->name('ruangan.show');

    /* ---------------- Maintenance -------------- */

    // daftar maintenance (bisa difilter per barang via query ?barang_id=)
    Route::get('/maintenance', [MaintenanceController::class, 'index'])
        ->name('maintenance.index');

    // buat permintaan maintenance untuk barang tertentu
    Route::get('/barang/{barang}/maintenance/create', [MaintenanceController::class, 'create'])
        ->name('maintenance.create');

    Route::post('/barang/{barang}/maintenance', [MaintenanceController::class, 'store'])
        ->name('maintenance.store');

    // edit/update data maintenance
    Route::get('/maintenance/{maintenance}/edit', [MaintenanceController::class, 'edit'])
        ->name('maintenance.edit');

    Route::put('/maintenance/{maintenance}', [MaintenanceController::class, 'update'])
        ->name('maintenance.update');

    // aksi khusus admin untuk maintenance
    Route::middleware('role:admin')->group(function () {
        Route::post('/maintenance/{maintenance}/approve',  [MaintenanceController::class, 'approve'])
            ->name('maintenance.approve');

        Route::post('/maintenance/{maintenance}/reject',   [MaintenanceController::class, 'reject'])
            ->name('maintenance.reject');

        Route::post('/maintenance/{maintenance}/complete', [MaintenanceController::class, 'complete'])
            ->name('maintenance.complete');

        Route::delete('/maintenance/{maintenance}',        [MaintenanceController::class, 'destroy'])
            ->name('maintenance.destroy');

        Route::get('/maintenance/pdf', [MaintenanceController::class, 'exportPdf'])
        ->name('maintenance.pdf');
        

    });

   Route::middleware(['auth','verified'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])
        ->name('notifications.readAll');

    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])
        ->name('notifications.read');
        
    Route::delete('/notifications/bulk-delete', [NotificationController::class, 'destroySelected'])
    ->name('notifications.destroySelected');
   });
   

});

require __DIR__.'/auth.php';
