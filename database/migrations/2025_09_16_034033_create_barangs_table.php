<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sakter');             
            $table->string('special_code')->index();  
            $table->string('kode_register')->unique(); 
            $table->string('kode_barang');             
            $table->unsignedInteger('nup');         
            $table->string('nama_barang');
            $table->string('merek')->nullable();
            $table->date('tgl_perolehan')->nullable();
            $table->text('keterangan')->nullable();

            $table->string('foto_url')->nullable();   
            $table->enum('kondisi', ['Baik','Rusak Ringan','Rusak Berat','Hilang'])->default('Baik');
            $table->decimal('nilai_perolehan', 15, 2)->nullable();

            $table->string('qr_string');   
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('sn')->nullable();
            $table->string('alternatif_qr');        

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
