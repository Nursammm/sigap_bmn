<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Barang;
use App\Notifications\MaintenanceNoteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class MaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status   = $request->query('status');
        $q        = trim((string) $request->query('q', ''));
        $from     = $request->query('from');
        $to       = $request->query('to');
        $barangId = $request->query('barang_id');

        $base = Maintenance::with(['barang','requester','approver'])
            ->when($barangId, fn($qq) => $qq->where('barang_id', $barangId))
            ->when($q !== '', function ($qq) use ($q) {
                $s = "%{$q}%";
                $qq->where(function ($w) use ($s) {
                    $w->where('uraian', 'like', $s)
                        ->orWhereHas('barang', function ($b) use ($s) {
                            $b->where('nama_barang', 'like', $s)
                                ->orWhere('kode_register', 'like', $s)
                                ->orWhere('kode_barang', 'like', $s);
                        })
                        ->orWhere('admin_note', 'like', $s);
                });
            })
            ->when(
                $status && in_array($status, ['Diajukan','Disetujui','Proses','Selesai','Ditolak'], true),
                fn($qq) => $qq->where('status', $status)
            )
            ->when($from, fn($qq) => $qq->whereDate('tanggal_mulai', '>=', $from))
            ->when($to, fn($qq) => $qq->whereDate('tanggal_mulai', '<=', $to))
            ->latest('tanggal_mulai')
            ->latest();

        $items = $base->paginate(15)->appends($request->query());
        $totalBiaya = (clone $base)->sum('biaya');

        $barangList = Maintenance::with('barang')
            ->whereNotNull('barang_id')
            ->select('barang_id')
            ->distinct()
            ->get()
            ->map(fn($m) => $m->barang)
            ->filter()
            ->sortBy('nama_barang')
            ->values();

        $maintenanceByBarang = Maintenance::with('barang')
            ->where('status', 'Selesai')
            ->orderBy('barang_id')
            ->latest('tanggal_mulai')
            ->get()
            ->groupBy('barang_id');

        return view('maintenance.index', compact(
            'items','totalBiaya','status','q','from','to','barangId','barangList','maintenanceByBarang'
        ));
    }


    public function create(Barang $barang)
{
    $isAdmin = Auth::user()->role === 'admin';

    $hasOpen = Maintenance::open()
        ->where('barang_id', $barang->id)
        ->exists();

    if (!$isAdmin && $hasOpen) {
        return redirect()
            ->route('maintenance.index', ['barang_id' => $barang->id])
            ->withErrors('Pengajuan maintenance untuk barang ini masih berjalan. Tunggu sampai disetujui/ditolak atau selesai.');
    }

    return view('maintenance.create', compact('barang'));
}


    public function store(Request $request, Barang $barang)
{
    $isAdmin = Auth::user()->role === 'admin';

    $hasOpen = Maintenance::open()
        ->where('barang_id', $barang->id)
        ->lockForUpdate()
        ->exists();

    if (!$isAdmin && $hasOpen) {
        return back()
            ->withErrors('Pengajuan maintenance untuk barang ini masih berjalan. Tidak bisa mengajukan lagi.')
            ->withInput();
    }

    $data = $request->validate([
        'tanggal_mulai'   => ['required','date'],
        'tanggal_selesai' => ['nullable','date','after_or_equal:tanggal_mulai'],
        'uraian'          => ['nullable','string','max:5000'],
        'biaya'           => ['nullable','numeric','min:0'],
        'status'          => ['nullable', \Illuminate\Validation\Rule::in(['Diajukan','Disetujui','Proses','Selesai','Ditolak'])],
        'photos'          => ['nullable','array','max:10'],
        'photos.*'        => ['image','mimes:jpg,jpeg,png,webp','max:2048'],
    ]);

    $status = $isAdmin ? ($data['status'] ?? 'Disetujui') : 'Diajukan';

    $m = Maintenance::create([
        'barang_id'       => $barang->id,
        'tanggal_mulai'   => $data['tanggal_mulai'],
        'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
        'uraian'          => $data['uraian'] ?? null,
        'biaya'           => (int) ($data['biaya'] ?? 0),
        'status'          => $status,
        'requested_by'    => Auth::id(),
        'approved_by'     => $status === 'Disetujui' ? Auth::id() : null,
        'photo_path'      => [],
    ]);

     if ($request->hasFile('photos')) {
            $stored = [];
            foreach ($request->file('photos') as $file) {
                $stored[] = $file->store("maintenance/{$m->id}", 'public');
            }
            $m->photo_path = $stored;
            $m->save();
        }

    return redirect()
        ->route('maintenance.index', ['barang_id' => $barang->id])
        ->with('ok', 'Pengajuan pemeliharaan tersimpan.');
}

    public function edit(Maintenance $maintenance)
    {
        $this->authorizeUpdate($maintenance);

        // view-mu memakai variabel $m
        return view('maintenance.edit', [
            'm' => $maintenance->load('barang'),
        ]);
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $this->authorizeUpdate($maintenance);
        $user = Auth::user();
        $isAdmin = Auth::user()->role === 'admin';

        $data = $request->validate([
            'tanggal_mulai'   => ['required','date'],
            'tanggal_selesai' => ['nullable','date','after_or_equal:tanggal_mulai'],
            'uraian'          => ['nullable','string','max:5000'],
            'biaya'           => ['nullable','numeric','min:0'],
            'status'          => ['nullable', Rule::in(['Diajukan','Disetujui','Proses','Selesai','Ditolak'])],
            'photos'          => ['nullable','array','max:10'],
            'photos.*'        => ['image','mimes:jpg,jpeg,png,webp','max:2048'],
            'remove_photos'   => ['nullable','array'],
            'remove_photos.*' => ['string'],
            'admin_note'      => ['nullable', 'string', 'max:5000']
        ]);

        $statusLama = $maintenance->status; 
        $noteLama = $maintenance->admin_note;


        $status = $maintenance->status;
        if ($isAdmin && isset($data['status'])) {
            $status = $data['status'];
            if (in_array($status, ['Disetujui','Ditolak'], true)) {
                $maintenance->approved_by = Auth::id();
            }
        }

        if ($isAdmin) 
            { 
                $maintenance->admin_note = $data['admin_note'] ?? null; 
            }

        $maintenance->fill([
            'tanggal_mulai'   => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
            'uraian'          => $data['uraian'] ?? null,
            'biaya'           => (int) ($data['biaya'] ?? 0),
            'status'          => $status,
        ])->save();

        $maintenance->save();

        $noteBerubah = $noteLama !== $maintenance->admin_note;

        if ($isAdmin && $noteBerubah) 
            { 
            $requester = $maintenance->requester; 
        if ($requester && $requester->id !== $user->id) 
            { 
            $requester->notify(new MaintenanceNoteNotification($maintenance)); 
        } }

        $existing = $maintenance->photo_path ?? [];

        if (!empty($data['remove_photos'])) {
            foreach ($data['remove_photos'] as $path) {
                if (in_array($path, $existing, true)) {
                    Storage::disk('public')->delete($path);
                }
            }
            $existing = array_values(array_diff($existing, $data['remove_photos']));
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $existing[] = $file->store("maintenance/{$maintenance->id}", 'public');
            }
        }

        $maintenance->photo_path = $existing;
        $maintenance->save();

        return redirect()
            ->route('maintenance.index', request()->query())
            ->with('ok','Pemeliharaan diperbarui.');
    }

    public function approve(Request $request, Maintenance $maintenance)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'admin_note' => ['nullable','string','max:2000'],
        ]);

        $maintenance->update([
            'status'      => 'Disetujui',
            'approved_by' => Auth::id(),
            'admin_note'  => $data['admin_note'] ?? null,
        ]);

        $requester = $maintenance->requester;
        if ($requester && $requester->id !== Auth::id()) {
            $requester->notify(new MaintenanceNoteNotification($maintenance));
        }

        return back()->with('ok', 'Pengajuan disetujui.');
    }

    public function reject(Request $request, Maintenance $maintenance)
    {
        $this->authorizeAdmin();

        $user = Auth::user();

        $data = $request->validate([
            'admin_note' => ['nullable','string','max:2000'],
        ]);

        $maintenance->update([
            'status'      => 'Ditolak',
            'approved_by' => $user->id,
        ]);

        $requester = $maintenance->requester;
        if ($requester && $requester->id !== $user->id) {
            $requester->notify(new MaintenanceNoteNotification($maintenance));
        }

        return back()->with('ok', 'Pengajuan ditolak.');
    }

    public function complete(Maintenance $maintenance)
    {
        $this->authorizeAdmin();

        $maintenance->update([
            'status'          => 'Selesai',
            'tanggal_selesai' => now(),
            'approved_by'     => $maintenance->approved_by ?: Auth::id(),
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'ok'      => true,
                'status'  => 'Selesai',
                'message' => 'Pengajuan ditandai selesai.',
            ]);
        }

        $requester = $maintenance->requester;
        if ($requester && $requester->id !== Auth::id()) {
            $requester->notify(new MaintenanceNoteNotification($maintenance));
        }

        return back()->with('ok','Pengajuan ditandai selesai.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $this->authorizeAdmin();

        if ($maintenance->photo_path) {
            Storage::disk('public')->delete($maintenance->photo_path);
        }

        $maintenance->delete();

        return back()->with('ok', 'Data pemeliharaan dihapus.');
    }

    protected function authorizeAdmin(): void
    {
        abort_unless(Auth::user()?->role === 'admin', 403);
    }

    protected function authorizeUpdate(Maintenance $m): void
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        if ($user->role === 'admin') {
            return;
        }

        abort_unless(
            $m->requested_by === $user->id &&
            !in_array($m->status, ['Selesai','Ditolak'], true),
            403
        );
    }

    public function exportPdf(Request $request)
{
    $maintenanceIds = collect((array) $request->input('maintenance_id'))
        ->filter(fn($id) => is_numeric($id))
        ->map(fn($id) => (int) $id)
        ->unique()
        ->values();

    if ($maintenanceIds->isEmpty()) {
        return back()->withErrors('Pilih minimal satu riwayat pemeliharaan untuk dicetak.');
    }

    $maintenances = Maintenance::with('barang')
        ->whereIn('id', $maintenanceIds)
        ->where('status', 'Selesai')
        ->orderBy('barang_id')
        ->orderBy('tanggal_mulai')
        ->get();

    if ($maintenances->isEmpty()) {
        return back()->withErrors('Data pemeliharaan tidak ditemukan atau belum berstatus selesai.');
    }

    $rows = $maintenances->map(function ($m) {
        return [
            'uraian' => $m->barang?->nama_barang ?? '-',
            'jumlah' => 1,
            'satuan' => 'Unit',
            'pagu'   => $m->biaya,
            'total'  => $m->biaya,
        ];
    });

    $pdf = Pdf::loadView('maintenance.pdf', [
        'rows'   => $rows,
        'kotaSurat' => 'Palu',
        'tanggalSurat' => now()->translatedFormat('d F Y'),
    ])->setPaper('A4', 'portrait');

    return $pdf->stream('permohonan-pemeliharaan.pdf');
}

}
