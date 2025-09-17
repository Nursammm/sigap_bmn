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
        'alternatif_qr'
    ];
    protected $casts = ['tgl_perolehan'=>'date','nilai_perolehan'=>'decimal:2'];


    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
