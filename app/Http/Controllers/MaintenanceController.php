<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Daftar semua pemeliharaan + filter */
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
                      ->orWhere('vendor', 'like', $s);
                });
            })
            ->when($status && in_array($status, ['Diajukan','Disetujui','Proses','Selesai','Ditolak'], true),
                fn($qq) => $qq->where('status', $status))
            ->when($from, fn($qq) => $qq->whereDate('tanggal_mulai', '>=', $from))
            ->when($to, fn($qq) => $qq->whereDate('tanggal_mulai', '<=', $to))
            ->latest('tanggal_mulai')
            ->latest();

        $items = $base->paginate(15)->appends($request->query());
        $totalBiaya = (clone $base)->sum('biaya');

        return view('maintenance.index', compact('items','totalBiaya','status','q','from','to','barangId'));
    }

    /** Form create untuk 1 barang */
    public function create(Barang $barang)
    {
        $jenisList = ['Preventive','Corrective','Kalibrasi','Perbaikan'];
        return view('maintenance.create', compact('barang','jenisList'));
    }

    /** Simpan pemeliharaan; lampiran ke disk public */
    public function store(Request $request, Barang $barang)
    {
        $jenisList = ['Preventive','Corrective','Kalibrasi','Perbaikan'];
        $isAdmin   = Auth::user()->role === 'admin';

        $data = $request->validate([
            'tanggal_mulai'   => ['required','date'],
            'tanggal_selesai' => ['nullable','date','after_or_equal:tanggal_mulai'],
            'jenis'           => ['required', Rule::in($jenisList)],
            'uraian'          => ['nullable','string','max:5000'],
            'biaya'           => ['nullable','numeric','min:0'],
            'vendor'          => ['nullable','string','max:255'],
            'status'          => ['nullable', Rule::in(['Diajukan','Disetujui','Proses','Selesai','Ditolak'])],
            'lampiran'        => ['nullable','file','max:5120','mimes:pdf,jpg,jpeg,png'],
        ]);

        $status = $isAdmin ? ($data['status'] ?? 'Disetujui') : 'Diajukan';

        $m = Maintenance::create([
            'barang_id'       => $barang->id,
            'tanggal_mulai'   => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
            'jenis'           => $data['jenis'],
            'uraian'          => $data['uraian'] ?? null,
            'biaya'           => (int) ($data['biaya'] ?? 0),
            'vendor'          => $data['vendor'] ?? null,
            'status'          => $status,
            'requested_by'    => Auth::id(),
            'approved_by'     => $status === 'Disetujui' ? Auth::id() : null,
        ]);

        // Simpan lampiran ke storage/app/public/maintenance/{id}/...
        if ($request->hasFile('lampiran')) {
            /** @var \Illuminate\Http\UploadedFile $file */
            $file = $request->file('lampiran');
            $name = 'lampiran-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();

            // ⬇️ gunakan UploadedFile::storeAs agar Intelephense happy
            $path = $file->storeAs("maintenance/{$m->id}", $name, 'public');

            $m->update(['lampiran_path' => $path]);
        }

        return redirect()
            ->route('maintenance.index', ['barang_id' => $barang->id])
            ->with('ok', 'Pengajuan pemeliharaan tersimpan.');
    }

    /** Form edit */
    public function edit(Maintenance $maintenance)
    {
        $this->authorizeUpdate($maintenance);
        $jenisList = ['Preventive','Corrective','Kalibrasi','Perbaikan'];
        return view('maintenance.edit', ['m' => $maintenance->load('barang'), 'jenisList' => $jenisList]);
    }

    /** Update; bisa ganti lampiran pada disk public */
    public function update(Request $request, Maintenance $maintenance)
    {
        $this->authorizeUpdate($maintenance);

        $jenisList = ['Preventive','Corrective','Kalibrasi','Perbaikan'];
        $isAdmin   = Auth::user()->role === 'admin';

        $data = $request->validate([
            'tanggal_mulai'   => ['required','date'],
            'tanggal_selesai' => ['nullable','date','after_or_equal:tanggal_mulai'],
            'jenis'           => ['required', Rule::in($jenisList)],
            'uraian'          => ['nullable','string','max:5000'],
            'biaya'           => ['nullable','numeric','min:0'],
            'vendor'          => ['nullable','string','max:255'],
            'status'          => ['nullable', Rule::in(['Diajukan','Disetujui','Proses','Selesai','Ditolak'])],
            'lampiran'        => ['nullable','file','max:5120','mimes:pdf,jpg,jpeg,png'],
        ]);

        // Pengelola tidak boleh langsung set status final
        $status = $maintenance->status;
        if ($isAdmin && isset($data['status'])) {
            $status = $data['status'];
            if (in_array($status, ['Disetujui','Ditolak'], true)) {
                $maintenance->approved_by = Auth::id();
            }
        }

        $maintenance->fill([
            'tanggal_mulai'   => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
            'jenis'           => $data['jenis'],
            'uraian'          => $data['uraian'] ?? null,
            'biaya'           => (int) ($data['biaya'] ?? 0),
            'vendor'          => $data['vendor'] ?? null,
            'status'          => $status,
        ])->save();

        if ($request->hasFile('lampiran')) {
            if ($maintenance->lampiran_path) {
                Storage::disk('public')->delete($maintenance->lampiran_path);
            }

            /** @var \Illuminate\Http\UploadedFile $file */
            $file = $request->file('lampiran');
            $name = 'lampiran-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();

            // ⬇️ gunakan storeAs, bukan Storage::putFileAs
            $path = $file->storeAs("maintenance/{$maintenance->id}", $name, 'public');

            $maintenance->update(['lampiran_path' => $path]);
        }

        return back()->with('ok','Pemeliharaan diperbarui.');
    }

    /** Admin approve */
    public function approve(Maintenance $maintenance)
    {
        $this->authorizeAdmin();
        $maintenance->update(['status' => 'Disetujui', 'approved_by' => Auth::id()]);
        return back()->with('ok','Pengajuan disetujui.');
    }

    /** Admin reject */
    public function reject(Maintenance $maintenance)
    {
        $this->authorizeAdmin();
        $maintenance->update(['status' => 'Ditolak', 'approved_by' => Auth::id()]);
        return back()->with('ok','Pengajuan ditolak.');
    }

    /** Hapus (admin) */
    public function destroy(Maintenance $maintenance)
    {
        $this->authorizeAdmin();
        // file lampiran terhapus via event model
        $maintenance->delete();
        return back()->with('ok','Data pemeliharaan dihapus.');
    }

    /**
     * Download lampiran dari disk public.
     * (Gantikan response() agar tidak kena warning Intelephense)
     */
    public function attachment(Maintenance $maintenance)
{
    abort_unless($maintenance->lampiran_path, 404);

    $absolute = storage_path('app/public/'.$maintenance->lampiran_path);
    abort_unless(is_file($absolute), 404);

    // kenali oleh Intelephense, dan cross-Laravel
    return response()->download($absolute);
}

    // ===== Helpers otorisasi =====

    protected function authorizeAdmin(): void
    {
        abort_unless(Auth::user()->role === 'admin', 403);
    }

    protected function authorizeUpdate(Maintenance $m): void
    {
        if (Auth::user()->role === 'admin') return;

        abort_unless(
            $m->requested_by === Auth::id() &&
            !in_array($m->status, ['Selesai','Ditolak'], true),
            403
        );
    }
}
