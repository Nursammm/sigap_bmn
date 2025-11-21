<?php

namespace App\Http\Controllers;

use App\Models\{Barang, Location, MutasiBarang, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\DatabaseNotification;
use App\Notifications\MutasiRequestedNotification;
use App\Notifications\MutasiRequestResolvedNotification;

class MutasiBarangController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    /* ===================== FORM MUTASI ===================== */

    public function create(Barang $barang)
    {
        return view('mutasi.create', [
            'barang'    => $barang->load('location'),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    /* ===================== ADMIN: MUTASI LANGSUNG ===================== */

    public function store(Request $request, Barang $barang)
    {
        $request->merge(['from_location_id' => $barang->location_id]);

        $data = $request->validate([
            'lokasi'            => ['required','string','max:255'],
            'tanggal'           => ['required','date'],
            'catatan'           => ['nullable','string','max:1000'],
            'from_location_id'  => ['nullable','exists:locations,id'],
        ]);

        $lokasiNama = trim($data['lokasi']);
        $toLocation = Location::firstOrCreate(['name' => $lokasiNama]);

        if ((int) $toLocation->id === (int) $barang->location_id) {
            return back()
                ->withErrors(['lokasi' => 'Lokasi tujuan tidak boleh sama dengan lokasi saat ini.'])
                ->withInput();
        }

        MutasiBarang::create([
            'barang_id'        => $barang->id,
            'from_location_id' => $data['from_location_id'],
            'to_location_id'   => $toLocation->id,
            'moved_by'         => Auth::id(),
            'tanggal'          => $data['tanggal'],
            'catatan'          => $data['catatan'] ?? null,
        ]);

        $barang->update(['location_id' => $toLocation->id]);

        return redirect()->route('barang.index')->with('success','Mutasi lokasi tersimpan.');
    }

    /* ===================== RIWAYAT MUTASI ===================== */

    public function index()
    {
        $items = MutasiBarang::with(['barang','fromLocation','toLocation','user'])
            ->latest('tanggal')->paginate(20);

        $totalBarangs = Barang::count();

        return view('mutasi.index', compact('items','totalBarangs'));
    }

    /* ===================== PENGELOLA: MENGAJUKAN MUTASI ===================== */

    public function requestMutasi(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'lokasi'  => ['required','string','max:255'],
            'tanggal' => ['required','date'],
            'catatan' => ['nullable','string','max:2000'],
        ]);

        $currentName = trim(optional($barang->location)->name ?? '');
        $toName      = trim($data['lokasi']);

        /* 1) CEGAH LOKASI TUJUAN SAMA */
        if ($currentName !== '' && strcasecmp($currentName, $toName) === 0) {
            return back()
                ->withErrors([
                    'lokasi' => 'Lokasi tujuan tidak boleh sama dengan lokasi saat ini.',
                ])
                ->withInput();
        }

        /* 2) CEGAH PERMINTAAN MUTASI YANG MASIH PENDING */
        $pending = DatabaseNotification::where('type', MutasiRequestedNotification::class)
            ->whereNull('read_at')
            ->whereJsonContains('data->barang_id', (int) $barang->id)
            ->whereJsonContains('data->requested_by_id', (int) $request->user()->id)
            ->exists();

        if ($pending) {
            return back()
                ->withErrors([
                    'lokasi' => 'Masih ada permintaan mutasi yang belum diproses Admin untuk barang ini.',
                ])
                ->withInput();
        }

        /* 3) KIRIM NOTIFIKASI KE ADMIN */
        $payload = [
            'type'              => 'mutasi.request',
            'barang_id'         => (int) $barang->id,
            'barang_nama'       => $barang->nama_barang,
            'kode_register'     => $barang->kode_register,
            'from_name'         => $currentName ?: '-',
            'to_name'           => $toName,
            'tanggal'           => $data['tanggal'],
            'catatan'           => $data['catatan'],
            'requested_by_id'   => $request->user()->id,
            'requested_by_name' => $request->user()->name,
        ];

        User::where('role','admin')->get()
            ->each(fn($admin) => $admin->notify(new MutasiRequestedNotification($payload)));

        return redirect()->route('barang.index')
            ->with('success','Permintaan mutasi dikirim. Menunggu persetujuan Admin.');
    }

    public function approveRequest(Request $request, string $notificationId)
        {
            /** @var \App\Models\User|null $user */
            $user = $request->user();
            abort_unless($user && $user->role === 'admin', 403);

            $validated = $request->validate([
                'note' => ['nullable','string','max:255'],
            ]);

            $notif = $user->notifications()
                ->whereKey($notificationId)
                ->firstOrFail();

            $data  = $notif->data;
            $barang = Barang::findOrFail($data['barang_id']);

            DB::transaction(function () use ($data, $barang, $notif, $user, $validated) {
                $toLoc = Location::firstOrCreate(['name' => trim($data['to_name'])]);

                MutasiBarang::create([
                    'barang_id'        => $barang->id,
                    'from_location_id' => $barang->location_id,
                    'to_location_id'   => $toLoc->id,
                    'moved_by'         => $user->id,
                    'tanggal'          => $data['tanggal'],
                    'catatan'          => $data['catatan'],
                ]);

                $barang->update(['location_id' => $toLoc->id]);

                $notif->markAsRead();

                DatabaseNotification::where('type', MutasiRequestedNotification::class)
                    ->whereNull('read_at')
                    ->whereJsonContains('data->barang_id', (int) $data['barang_id'])
                    ->whereJsonContains('data->requested_by_id', (int) $data['requested_by_id'])
                    ->update(['read_at' => now()]);

                $payload = [
                    'type'          => 'mutasi.resolved',
                    'status'        => 'Approved',
                    'barang_id'     => $barang->id,
                    'barang_nama'   => $barang->nama_barang,
                    'kode_register' => $barang->kode_register,
                    'to_name'       => $toLoc->name,
                    'tanggal'       => $data['tanggal'],
                    'decided_by'    => $user->name,
                    'note'          => $validated['note'] ?? null,
                ];

                optional(User::find($data['requested_by_id']))
                    ?->notify(new MutasiRequestResolvedNotification($payload));
            });

            return back()->with('success','Permintaan mutasi disetujui.');
        }


    public function rejectRequest(Request $request, string $notificationId)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
        abort_unless($user && $user->role === 'admin', 403);

        $validated = $request->validate([
            'note' => ['nullable','string','max:255'],
        ]);

        $notif = $user->notifications()
            ->whereKey($notificationId)
            ->firstOrFail();

        $data  = $notif->data;

        $notif->markAsRead();

        DatabaseNotification::where('type', MutasiRequestedNotification::class)
            ->whereNull('read_at')
            ->whereJsonContains('data->barang_id', (int) $data['barang_id'])
            ->whereJsonContains('data->requested_by_id', (int) $data['requested_by_id'])
            ->update(['read_at' => now()]);

        $payload = [
            'type'          => 'mutasi.resolved',
            'status'        => 'Rejected',
            'barang_id'     => (int) $data['barang_id'],
            'barang_nama'   => $data['barang_nama'],
            'kode_register' => $data['kode_register'],
            'to_name'       => $data['to_name'],
            'tanggal'       => $data['tanggal'],
            'decided_by'    => $user->name,
            'note'          => $validated['note'] ?? null,
        ];

        optional(User::find($data['requested_by_id']))
            ?->notify(new MutasiRequestResolvedNotification($payload));

        return back()->with('success','Permintaan mutasi ditolak.');
    }

}
