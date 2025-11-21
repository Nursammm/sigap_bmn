<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = [
        'barang_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'uraian',
        'biaya',
        'status',
        'requested_by',
        'approved_by',
        'photo_path',
        'admin_note', 
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'biaya'           => 'integer',
        'photo_path'      => 'array',
    ];

    public function scopeOpen($q)
    {
        return $q->whereIn('status', ['Diajukan','Disetujui','Proses']);
    }

    // Relasi
    public function barang()    { return $this->belongsTo(Barang::class, 'barang_id'); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver()  { return $this->belongsTo(User::class, 'approved_by'); }
}
