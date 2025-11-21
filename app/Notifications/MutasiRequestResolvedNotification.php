<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MutasiRequestResolvedNotification extends Notification
{
    use Queueable;

    public function __construct(public array $payload) {}

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return $this->payload + [
            'type'   => 'mutasi.resolved',
            '_class' => static::class,
        ];
    }

    public function toArray($notifiable) { return $this->toDatabase($notifiable); }
}
