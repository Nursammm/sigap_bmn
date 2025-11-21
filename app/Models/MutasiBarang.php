<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    protected $fillable = [
        'barang_id',
        'from_location_id',
        'to_location_id',
        'moved_by',
        'tanggal',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi
    public function barang()
    {
        // Jika Barang pakai SoftDeletes dan ingin tetap tampil di riwayat:
        // return $this->belongsTo(Barang::class, 'barang_id')->withTrashed();
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function user()
    {
        // user yang memindahkan barang (admin yg approve / user yg melakukan store)
        return $this->belongsTo(User::class, 'moved_by');
    }
}
