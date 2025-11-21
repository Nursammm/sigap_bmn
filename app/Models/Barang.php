<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_sakter',
        'special_code',
        'kode_register',
        'kategori_id',
        'kode_barang',
        'nup',
        'nama_barang',
        'merek',
        'tgl_perolehan',
        'keterangan',
        'foto_url',
        'kondisi',
        'nilai_perolehan',
        'qr_string',
        'location_id',
        'sn',
        'alternatif_qr',
    ];

    protected $casts = [
        'tgl_perolehan'   => 'date',
        'nilai_perolehan' => 'decimal:2',
        'foto_url'        => 'array',
    ];

    // ===== Relasi
    public function location()     { return $this->belongsTo(Location::class, 'location_id'); }
    public function mutasi()       { return $this->hasMany(MutasiBarang::class, 'barang_id'); }
    public function maintenances() { return $this->hasMany(Maintenance::class, 'barang_id'); }
    public function category()     { return $this->belongsTo(Kategori::class, 'kategori_id'); }

    // ===== (Opsional) Sabuk pengaman hard-delete anak
    protected static function booted()
    {
        static::deleting(function (Barang $barang) {
            // Jika FK ON DELETE CASCADE sudah benar, blok ini tidak wajib.
            // Tetap kita jaga-jaga untuk data lama / FK belum cascade.
            $barang->mutasi()->delete();
            $barang->maintenances()->delete();
        });
    }
}
