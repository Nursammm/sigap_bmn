<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletionRequest extends Model
{
    protected $fillable = [
        'barang_id', 'requested_by', 'reason', 'status',
        'decided_by', 'decided_at', 'decision_note',
    ];

    public function requester(){ return $this->belongsTo(User::class, 'requested_by'); }
    public function decider(){   return $this->belongsTo(User::class, 'decided_by');   }
    public function barang(){    return $this->belongsTo(Barang::class, 'barang_id');  }
}