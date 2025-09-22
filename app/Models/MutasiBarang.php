<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    protected $fillable = [
        'barang_id','from_location_id','to_location_id','moved_by','tanggal','catatan'
    ];

    protected $casts = ['tanggal' => 'date'];

    public function barang()   
    { 
        return $this->belongsTo(Barang::class);
    }
    public function from()     
    { 
        return $this->belongsTo(Location::class, 'from_location_id'); 
    }
    public function to()       
    { 
        return $this->belongsTo(Location::class, 'to_location_id'); 
    }
    public function mover()    
    { 
        return $this->belongsTo(User::class, 'moved_by'); 
    }
}
