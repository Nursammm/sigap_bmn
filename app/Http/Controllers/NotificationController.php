<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\MutasiRequestedNotification;
use App\Notifications\MutasiRequestResolvedNotification;
use App\Notifications\MaintenanceNoteNotification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $user    = $request->user();
        $isAdmin = $user->role === 'admin';

        $types = [
            MutasiRequestedNotification::class,
            MutasiRequestResolvedNotification::class,
            MaintenanceNoteNotification::class,
        ];

        $filter = $request->query('filter', 'all'); // all | unread

        $query = $user->notifications()
            ->whereIn('type', $types)
            ->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(15);

        $badgeUnread = $user->unreadNotifications()
            ->whereIn('type', $types)
            ->count();

        return view('notifications.index', compact(
            'notifications',
            'badgeUnread',
            'filter',
            'isAdmin'
        ));
    }

    public function read(Request $request, string $id)
    {
        $user = $request->user();

        $notification = $user->notifications()
            ->where('id', $id)
            ->firstOrFail();

        // tandai satu notifikasi sebagai dibaca
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('okk', 'Notifikasi ditandai dibaca.');
    }

    public function readAll(Request $request)
    {
        $user    = $request->user();
        $isAdmin = $user->role === 'admin';

        $query = $user->unreadNotifications();

        if ($isAdmin) {
            // ADMIN: JANGAN MENANDAI NOTIFIKASI MUTASI
            $query->whereNotIn('type', [
                MutasiRequestedNotification::class,
                MutasiRequestResolvedNotification::class,
            ]);
        }
        // PENGELOLA: biarkan semua tipe (mutasi + maintenance) ikut ditandai

        $query->update(['read_at' => now()]);

        return back()->with('okk', 'Notifikasi sudah ditandai dibaca.');
    }
    
    public function destroySelected(Request $request)
{
    $user = $request->user();
    $ids  = (array) $request->input('ids', []);

    if (empty($ids)) {
        return back()->with('ok', 'Tidak ada notifikasi yang dipilih.');
    }

    // Hapus notifikasi milik user ini saja
    $user->notifications()
        ->whereIn('id', $ids)
        ->delete();

    return back()->with('okk', 'Notifikasi terpilih berhasil dihapus.');
}
}
