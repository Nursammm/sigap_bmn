<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mutasi_barangs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $t->foreignId('from_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $t->foreignId('to_location_id')->constrained('locations')->cascadeOnDelete();
            $t->foreignId('moved_by')->constrained('users')->cascadeOnDelete();
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
