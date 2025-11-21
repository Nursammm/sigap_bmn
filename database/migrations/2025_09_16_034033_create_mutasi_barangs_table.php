<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mutasi_barangs', function (Blueprint $t) {
            $t->id();

            // Hapus barang -> mutasi ikut hard-delete (CASCADE)
            $t->foreignId('barang_id')
              ->constrained('barangs')
              ->cascadeOnDelete();

            // Jika lokasi dihapus, riwayat tidak hilang -> set NULL
            $t->foreignId('from_location_id')
              ->nullable()
              ->constrained('locations')
              ->nullOnDelete();

            // Dulu CASCADE, ubah ke NULL agar riwayat tetap ada
            $t->foreignId('to_location_id')
              ->nullable()
              ->constrained('locations')
              ->nullOnDelete();

            // Jika user dihapus, riwayat tetap ada -> set NULL
            $t->foreignId('moved_by')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();

            $t->date('tanggal');
            $t->text('catatan')->nullable();

            $t->timestamps();

            $t->index(['barang_id','tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_barangs');
    }
};
