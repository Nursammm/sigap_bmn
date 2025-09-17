<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name'];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'location_id');
    }
}
