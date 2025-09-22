<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $t) {
            $t->id();
            $t->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $t->date('tanggal_mulai');
            $t->date('tanggal_selesai')->nullable();
            $t->string('jenis', 30); // Preventive, Corrective, Kalibrasi, Perbaikan
            $t->text('uraian')->nullable();
            $t->unsignedBigInteger('biaya')->default(0);
            $t->string('vendor')->nullable();

            $t->string('status', 20)->default('Diajukan'); // Diajukan, Disetujui, Proses, Selesai, Ditolak
            $t->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $t->string('lampiran_path')->nullable(); // disimpan di NAS
            $t->timestamps();

            $t->index(['barang_id', 'tanggal_mulai']);
            $t->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
