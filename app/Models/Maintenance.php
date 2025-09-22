<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Maintenance extends Model
{
    protected $fillable = [
        'barang_id','tanggal_mulai','tanggal_selesai','jenis','uraian','biaya',
        'vendor','status','requested_by','approved_by','lampiran_path'
    ];

     protected $casts   = ['tanggal_mulai'=>'date','tanggal_selesai'=>'date'];
    protected $appends = ['lampiran_url'];

    public function barang()    { return $this->belongsTo(\App\Models\Barang::class,'barang_id'); }
    public function requester() { return $this->belongsTo(\App\Models\User::class,'requested_by'); }
    public function approver()  { return $this->belongsTo(\App\Models\User::class,'approved_by'); }

    public function getLampiranUrlAttribute(): ?string
    {
        return $this->lampiran_path ? asset('storage/'.$this->lampiran_path) : null;
    }

    protected static function booted()
    {
        static::deleting(function (self $m) {
            if ($m->lampiran_path) Storage::disk('public')->delete($m->lampiran_path);
        });
    }
}
