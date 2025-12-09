<?php

namespace App\Notifications;

use App\Models\Maintenance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MaintenanceNoteNotification extends Notification
{
    use Queueable;

    public function __construct(public Maintenance $maintenance)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $barang = $this->maintenance->barang;

        return [
            'maintenance_id'=> $this->maintenance->id,
            'barang_id'     => $this->maintenance->barang_id,
            'barang_nama'   => $barang?->nama_barang,
            'kode_register' => $barang?->kode_register,
            'status'        => $this->maintenance->status,
            'message'       => 'Pemeliharaan barang diperbarui.',
            'admin_note'    => $this->maintenance->admin_note,
        ];
    }
}
