<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $t) {
            $t->id();

            // Relasi utama
            $t->foreignId('barang_id')
              ->constrained('barangs')
              ->cascadeOnDelete();

            // Data inti
            $t->date('tanggal_mulai');
            $t->date('tanggal_selesai')->nullable();
            $t->text('uraian')->nullable();

            // Biaya & lampiran
            $t->unsignedBigInteger('biaya')->default(0);
            $t->string('photo_path')->nullable(); 
            // Status & catatan admin
            $t->string('status', 20)->default('Diajukan'); // Diajukan, Disetujui, Proses, Selesai, Ditolak
            $t->text('admin_note')->nullable();

            // Pelaku
            $t->foreignId('requested_by')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();

            $t->foreignId('approved_by')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();

            $t->timestamps();

            // Indeks
            $t->index(['barang_id', 'tanggal_mulai']);
            $t->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
