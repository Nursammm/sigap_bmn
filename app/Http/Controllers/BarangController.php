<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::with('location')->get();
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        // Ambil lokasi
        $locations = Location::orderBy('name')->get(['id', 'name']);

        // Ambil pasangan nama_barang -> kode_barang dari data yang sudah ada
        $nameCodeMap = Barang::select('nama_barang', 'kode_barang')
            ->whereNotNull('nama_barang')
            ->whereNotNull('kode_barang')
            ->distinct()
            ->orderBy('nama_barang')
            ->get()
            ->pluck('kode_barang', 'nama_barang'); // hasil: ['Nama Barang' => 'KODE123', ...]

        return view('barang.create', [
            'title'        => 'Tambah Barang',
            'locations'    => $locations,
            'nameCodeMap'  => $nameCodeMap, // untuk JS auto-fill
        ]);
    }

    public function show(Barang $barang)
    {
        return view('barang.show', compact('barang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // tidak ada lagi 'kategori'
            'nama_barang'     => 'required|string|max:255',
            'kode_barang'     => 'nullable|string|max:100',
            'merek'           => 'nullable|string|max:255',

            'location_id'     => 'nullable',
            'lokasi_baru'     => 'nullable|string|max:255',

            'keterangan'      => 'nullable|string|max:255',
            'tgl_perolehan'   => 'nullable|date',
            'nilai_perolehan' => 'nullable|numeric|min:0',
            'kondisi'         => 'nullable|string|max:50',
            'kode_sakter'     => 'nullable|string|max:100',
            'kode_register'   => 'nullable|string|max:100',
            'sn'              => 'nullable|string|max:100',

            'foto_url'        => 'nullable|array',
            'foto_url.*'      => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        /**
         * ==================== LOGIKA LOKASI ====================
         */
        $locationId = $validated['location_id'] ?? null;

        if ($locationId === 'other') {
            // Kalau user memilih "Lainnya", wajib isi lokasi_baru
            $request->validate([
                'lokasi_baru' => 'required|string|max:255',
            ]);

            $location = Location::firstOrCreate(['name' => $request->lokasi_baru]);
            $locationId = $location->id;
        } elseif ($locationId) {
            // kalau pilih salah satu dari dropdown, pakai itu saja
            // tidak perlu apa-apa
        } else {
            // Tidak pilih apapun di dropdown
            if ($request->filled('lokasi_baru')) {
                // kalau dia tetap mengisi lokasi_baru, buat lokasi baru
                $location = Location::firstOrCreate(['name' => $request->lokasi_baru]);
                $locationId = $location->id;
            } else {
                // benar-benar kosong -> lokasi optional
                $locationId = null;
            }
        }

        /**
         * ==================== AUTO-FILL KODE BARANG ====================
         * - Jika form sudah mengisi kode_barang -> pakai itu (manual).
         * - Jika kosong: cek apakah sudah pernah ada barang dengan nama yang sama.
         *   Kalau ada, pakai kode_barang dari data lama.
         *   Kalau tetap tidak ada, paksa user isi manual.
         */
        $kodeBarang = $validated['kode_barang'] ?? null;

        if (!$kodeBarang) {
            $kodeBarang = Barang::where('nama_barang', $validated['nama_barang'])
                ->whereNotNull('kode_barang')
                ->orderByDesc('id')
                ->value('kode_barang');
        }

        if (!$kodeBarang) {
            return back()->withErrors([
                'kode_barang' => 'Kode barang belum tersedia untuk nama ini. Silakan isi kode barang secara manual.',
            ])->withInput();
        }

        /**
         * ==================== HITUNG NUP BERDASARKAN KODE BARANG ====================
         */
        $latestNup = Barang::where('kode_barang', $kodeBarang)->max('nup') ?? 0;
        $nup       = $latestNup + 1;

        $specialCode = $kodeBarang . str_pad($nup, 1, '0', STR_PAD_LEFT);

        /**
         * ==================== UPLOAD MULTI FOTO ====================
         */
        $gambarPath = [];
        if ($request->hasFile('foto_url')) {
            foreach ($request->file('foto_url') as $file) {
                $gambarPath[] = $file->store('barang', 'public');
            }
        }

        /**
         * ==================== SIMPAN BARANG ====================
         */
        $barang = Barang::create([
            // tidak ada lagi 'kategori_id'
            'nama_barang'     => $validated['nama_barang'],
            'kode_barang'     => $kodeBarang,
            'merek'           => $validated['merek'] ?? null,
            'location_id'     => $locationId,
            'nilai_perolehan' => $validated['nilai_perolehan'] ?? 0,
            'kondisi'         => $validated['kondisi'] ?? 'Baik',
            'nup'             => $nup,
            'special_code'    => $specialCode,
            'kode_register'   => $validated['kode_register'] ?? null,
            'kode_sakter'     => $validated['kode_sakter'],
            'sn'              => $validated['sn'],
            'qr_string'       => $validated['kode_register'] ?? null,
            'alternatif_qr'   => ($validated['kode_sakter'] ?? '') . '*' . $kodeBarang . '*' . $nup,
            'foto_url'        => $gambarPath,
            'keterangan'      => $validated['keterangan'],
            'tgl_perolehan'   => $validated['tgl_perolehan'] ?? null,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        $locations = Location::orderBy('name')->get(['id','name']);

        return view('barang.edit', [
            'title'     => 'Edit Barang',
            'barang'    => $barang,
        ]);
    }
    /*
     * ==========================================================
     * UPDATE BARANG — SUDAH MENDUKUNG MULTIPLE FOTO
     * ==========================================================
     */
    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'nama_barang'     => 'required|string|max:255',
            'kode_barang'     => 'required|string|max:100',
            'merek'           => 'nullable|string|max:255',
            'nilai_perolehan' => 'nullable|numeric|min:0',
            'kondisi'         => 'nullable|string|max:50',
            'kode_sakter'     => 'nullable|string|max:100',
            'sn'              => 'nullable|string|max:100',
            'tgl_perolehan'   => 'nullable|date',

            // Multiple file
            'foto_url'        => 'nullable|array',
            'foto_url.*'      => 'image|mimes:jpg,jpeg,png|max:2048',

            'existing_photos' => 'nullable|array',
        ]);

        /**
         * ============== UPDATE FIELD BARANG ==============
         */
        $barang->fill([
            'nama_barang'     => $validated['nama_barang'],
            'kode_barang'     => $validated['kode_barang'],
            'merek'           => $validated['merek'] ?? null,
            'nilai_perolehan' => $validated['nilai_perolehan'] ?? 0,
            'kondisi'         => $validated['kondisi'] ?? 'Baik',
            'kode_sakter'     => $validated['kode_sakter'] ?? null,
            'sn'              => $validated['sn'] ?? null,
            'tgl_perolehan'   => $validated['tgl_perolehan'] ?? null,
        ]);

        // Re-generate special_code & alternatif_qr
        $barang->special_code = $barang->kode_barang . str_pad($barang->nup, 1, '0', STR_PAD_LEFT);
        $barang->alternatif_qr = ($barang->kode_sakter ?? '') . '*' .
            $barang->kode_barang . '*' . str_pad($barang->nup, 1, '0', STR_PAD_LEFT);

        // kode_register biarkan apa adanya (kalau kamu mau logic lain, tinggal tambah)

        /**
         * ============== MULTIPLE FOTO (tambah tanpa menghapus lama) ==============
         */
        $currentPhotos = $barang->foto_url ?? [];

        if ($request->hasFile('foto_url')) {
            foreach ($request->file('foto_url') as $file) {
                $currentPhotos[] = $file->store('barang', 'public');
            }
        }

        $barang->foto_url = $currentPhotos;
        $barang->save();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }


    public function destroy(Request $request, Barang $barang)
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}
