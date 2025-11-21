<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Location;
use App\Models\Kategori;
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
        $kategoris = Kategori::orderBy('name')->get(['id', 'name']);

        $namesBykategori = Barang::select('kategori_id', 'nama_barang')
            ->whereNotNull('kategori_id')
            ->whereNotNull('nama_barang')
            ->distinct()
            ->orderBy('nama_barang')
            ->get()
            ->groupBy('kategori_id')
            ->map(fn($g) => $g->pluck('nama_barang')->values())
            ->toArray();

        $locations = Location::orderBy('name')->get(['id', 'name']);

        return view('barang.create', [
            'title'           => 'Tambah Barang',
            'kategoris'       => $kategoris,
            'namesByCategory' => $namesBykategori,
            'locations'       => $locations,
        ]);
    }

    public function show(Barang $barang)
    {
        return view('barang.show', compact('barang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori'        => 'required|string|max:100',
            'nama_barang'     => 'required|string|max:255',
            'kode_barang'     => 'nullable|string|max:100',
            'merek'           => 'nullable|string|max:255',

            'location_id'     => 'nullable',
            'lokasi_baru'     => 'nullable|string|max:255',

            'keterangan'      => 'nullable|string|max:255',
            'tgl_perolehan'   => 'required|date',
            'nilai_perolehan' => 'nullable|numeric|min:0',
            'kondisi'         => 'nullable|string|max:50',
            'kode_sakter'     => 'nullable|string|max:100',
            'kode_register'   => 'nullable|string|max:100',
            'sn'              => 'nullable|string|max:100',

            'foto_url'        => 'nullable|array',
            'foto_url.*'      => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $locationId = $validated['location_id'] ?? null;

        if ($locationId === 'other') {
            $request->validate([
                'lokasi_baru' => 'required|string|max:255',
            ]);

            $location = Location::firstOrCreate(['name' => $request->lokasi_baru]);
            $locationId = $location->id;
        } elseif ($locationId) {
        } else {
            if ($request->filled('lokasi_baru')) {
                $location = Location::firstOrCreate(['name' => $request->lokasi_baru]);
                $locationId = $location->id;
            } else {
                return back()->withErrors([
                    'location_id' => 'Silakan pilih lokasi atau isi lokasi baru.',
                ])->withInput();
            }
        }

        $catName  = trim($validated['kategori']);
        $kategori = Kategori::firstOrCreate(['name' => $catName]);

        if (!$kategori->wasRecentlyCreated) {
            $allowed = Barang::where('kategori_id', $kategori->id)
                ->distinct()
                ->pluck('nama_barang')
                ->toArray();

            if (!in_array($validated['nama_barang'], $allowed)) {
                return back()->withErrors([
                    'nama_barang' => 'Nama barang harus dipilih dari kategori yang dipilih.',
                ])->withInput();
            }
        }

        $latestNup = Barang::where('kode_barang', $validated['kode_barang'])->max('nup') ?? 0;
        $nup       = $latestNup + 1;

        $specialCode = ($validated['kode_barang'] ?? '') . str_pad($nup, 1, '0', STR_PAD_LEFT);

        $gambarPath = [];
        if ($request->hasFile('foto_url')) {
            foreach ($request->file('foto_url') as $file) {
                $gambarPath[] = $file->store('barang', 'public');
            }
        }

        $barang = Barang::create([
            'kategori_id'     => $kategori->id,
            'nama_barang'     => $validated['nama_barang'],
            'kode_barang'     => $validated['kode_barang'],
            'merek'           => $validated['merek'] ?? null,
            'location_id'     => $locationId,
            'nilai_perolehan' => $validated['nilai_perolehan'] ?? 0,
            'kondisi'         => $validated['kondisi'] ?? 'Baik',
            'nup'             => $nup,
            'special_code'    => $specialCode,
            'kode_register'   => $validated['kode_register'],
            'kode_sakter'     => $validated['kode_sakter'],
            'sn'              => $validated['sn'],
            'qr_string'       => $validated['kode_register'],
            'alternatif_qr'   => ($validated['kode_sakter'] ?? '') . '*' . $validated['kode_barang'] . '*' . $nup,
            'foto_url'        => $gambarPath,
            'keterangan'      => $validated['keterangan'],
            'tgl_perolehan'   => $validated['tgl_perolehan'],
        ]);

        if (empty($barang->kode_register)) {
            $barang->kode_register = 'REG-' . str_pad((string) $barang->id, 6, '0', STR_PAD_LEFT);
            $barang->save();
        }

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', [
            'title'  => 'Edit Barang',
            'barang' => $barang,
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
            'lokasi'          => 'required|string|max:255',
            'nilai_perolehan' => 'nullable|numeric|min:0',
            'kondisi'         => 'nullable|string|max:50',
            'kode_sakter'     => 'nullable|string|max:100',
            'sn'              => 'nullable|string|max:100',

            // Multiple file
            'foto_url'        => 'nullable|array',
            'foto_url.*'      => 'image|mimes:jpg,jpeg,png|max:2048',

            // untuk foto lama (jika ada yang ingin dihapus)
            'existing_photos' => 'nullable|array',
        ]);

        $lokasi = Location::firstOrCreate(
            ['name' => $validated['lokasi']],
            ['name' => $validated['lokasi']]
        );

        $barang->fill([
            'nama_barang'     => $validated['nama_barang'],
            'kode_barang'     => $validated['kode_barang'],
            'merek'           => $validated['merek'] ?? null,
            'location_id'     => $lokasi->id,
            'nilai_perolehan' => $validated['nilai_perolehan'] ?? 0,
            'kondisi'         => $validated['kondisi'] ?? 'Baik',
        ]);

        $barang->special_code = $barang->kode_barang . str_pad($barang->nup, 1, '0', STR_PAD_LEFT);
        $barang->alternatif_qr = ($barang->kode_sakter ?? '') . '*' .
            $barang->kode_barang . '*' . str_pad($barang->nup, 1, '0', STR_PAD_LEFT);

        if (empty($barang->kode_register)) {
            $barang->kode_register = 'REG-' . str_pad((string) $barang->id, 6, '0', STR_PAD_LEFT);
        }

        /*
         * ======================== MULTIPLE FOTO UPDATE ========================
         * - Tidak menghapus semua foto lama.
         * - Hanya menambah foto baru.
         * ======================================================================
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
