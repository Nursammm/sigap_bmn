@props(['max' => 10])

@php
    $user = auth()->user();
    $countUnread = 0;
    $items = collect();

    if ($user) {
        $types = [
            \App\Notifications\MutasiRequestedNotification::class,
            \App\Notifications\MutasiRequestResolvedNotification::class,
            \App\Notifications\DeletionRequestedNotification::class,
            \App\Notifications\DeletionRequestResolvedNotification::class,
            \App\Notifications\MaintenanceNoteNotification::class,
        ];

        $query = $user->unreadNotifications()
            ->whereIn('type', $types)
            ->latest();

        $countUnread = (clone $query)->count();
        $items = $query->take($max)->get();
    }

    // badge
    if ($countUnread > 99) {
        $badge = '99+';
    } elseif ($countUnread > 9) {
        $badge = '9+';
    } else {
        $badge = (string) $countUnread;
    }
@endphp

<div class="px-3 text-white">
    <a href="{{ route('notifications.index') }}"
       class="w-full flex items-center gap-3 rounded-md p-2 bg-white/5 hover:bg-white/10 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M14.857 17.082A23.85 23.85 0 0 1 12 17.25c-1.003 0-1.985-.07-2.943-.205a4.5 4.5 0 1 1 5.8 0Z" />
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M8.25 9V8.25A3.75 3.75 0 0 1 12 4.5v0a3.75 3.75 0 0 1 3.75 3.75V9
                     c0 1.012.24 2.008.7 2.9l.473.91c.392.753-.15 1.69-.999 1.69H8.076
                     c-.85 0-1.392-.937-.999-1.69l.473-.91A6.75 6.75 0 0 0 8.25 9Z" />
        </svg>

        <div class="text-sm font-medium">Notifikasi</div>

        @if($user)
            <span class="ml-auto inline-flex items-center justify-center rounded-full
                         {{ $countUnread ? 'bg-red-500 text-white' : 'bg-white/20 text-white/70' }}
                         text-[10px] min-w-5 h-5 px-1">
                {{ $badge }}
            </span>
        @endif
    </a>
</div>
