<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    protected $fillable = [
        'barang_id', 'from_sakter_id', 'to_sakter_id', 'tanggal'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function fromSakter()
    {
        return $this->belongsTo(Location::class, 'from_sakter_id');
    }

    public function toSakter()
    {
        return $this->belongsTo(Location::class, 'to_sakter_id');
    }
}
