<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MutasiRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(public array $payload) {}

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        // disimpan di kolom `data` tabel notifications
        return $this->payload + ['_class' => static::class];
    }

    public function toArray($notifiable) { return $this->toDatabase($notifiable); }
}
