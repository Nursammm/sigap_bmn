<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mutasi_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->foreignId('from_sakter_id')->constrained('kode_sakter')->onDelete('cascade');
            $table->foreignId('to_sakter_id')->constrained('kode_sakter')->onDelete('cascade');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('mutasi_barangs');
    }
};
