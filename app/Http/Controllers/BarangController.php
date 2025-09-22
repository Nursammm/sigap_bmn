<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Location;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $kategoris = Kategori::orderBy('name')->get(['id','name']);

    // daftar nama_barang unik per kategori_id -> ["1" => ["Laptop","Printer"], ...]
    $namesBykategori = Barang::select('kategori_id','nama_barang')
        ->whereNotNull('kategori_id')
        ->whereNotNull('nama_barang')
        ->distinct()
        ->orderBy('nama_barang')
        ->get()
        ->groupBy('kategori_id')
        ->map(fn($g) => $g->pluck('nama_barang')->values())
        ->toArray();

        return view('barang.create', [
            'title' => 'Tambah Barang',
            'kategoris'      => $kategoris,
            'namesByCategory' => $namesBykategori,
        ]);
    }
    public function show(Barang $barang)
{
    return view('barang.show', compact('barang'));

}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori'        => 'required','string','max:100',
            'nama_barang'     => 'required|string|max:255',
            'kode_barang'     => 'required|string|max:100',
            'merek'           => 'nullable|string|max:255',
            'lokasi'          => 'required|string|max:255', 
            'keterangan'      => 'nullable|string|max:255', 
            'tgl_perolehan'   => 'required|date|max:255', 
            'nilai_perolehan' => 'nullable|numeric|min:0',
            'kondisi'         => 'nullable|string|max:50',
            'kode_sakter'     => 'nullable|string|max:100',
            'kode_register'   => 'nullable|string|max:100',
            'foto_url'        => 'nullable|image|max:2048'
        ]);

        /**
         * 1. Cek apakah lokasi yang diinput sudah ada di tabel locations
         */
        $lokasi = Location::firstOrCreate(
            ['name' => $validated['lokasi']],
            ['name' => $validated['lokasi']]  
        );

        // Temukan / buat kategori
        $catName = trim($validated['kategori']);
        $kategori = Kategori::firstOrCreate(['name' => $catName]);

        if (!$kategori->wasRecentlyCreated) {
        $allowed = Barang::where('kategori_id', $kategori->id)
            ->distinct()->pluck('nama_barang')->toArray();

        if (!in_array($validated['nama_barang'], $allowed)) {
            return back()->withErrors([
                'nama_barang' => 'Nama barang harus dipilih dari kategori yang dipilih.'
            ])->withInput();
        }
    }



        /**
         * 2. Hitung NUP untuk kode_barang ini
         */
        $latestNup = Barang::where('kode_barang', $validated['kode_barang'])->max('nup') ?? 0;
        $nup = $latestNup + 1;

        $specialCode = $validated['kode_barang'] . str_pad($nup, 1, '0', STR_PAD_LEFT);

        /**
         * 3. Upload foto jika ada
         */
        $gambarPath = $request->file('foto_url')
            ? $request->file('foto_url')->store('barang', 'public')
            : null;

        /**
         * 4. Simpan barang
         */
        $barang = Barang::create([
            'kategori_id'     => $kategori->id,
            'nama_barang'     => $validated['nama_barang'],
            'kode_barang'     => $validated['kode_barang'],
            'merek'           => $validated['merek'] ?? null,
            'location_id'     => $lokasi->id,
            'nilai_perolehan' => $validated['nilai_perolehan'] ?? 0,
            'kondisi'         => $validated['kondisi'] ?? 'Baik',
            'nup'             => $nup,
            'special_code'    => $specialCode,
            'kode_register'   => $validated['kode_register'],
            'kode_sakter'     => $validated['kode_sakter'],
            'qr_string'       => $validated['kode_register'],
            'alternatif_qr'   => ($validated['kode_sakter'] ?? '') . $validated['kode_barang'] . $nup,
            'foto_url'        => $gambarPath,
            'keterangan'      => $validated['keterangan'],
            'tgl_perolehan'   => $validated['tgl_perolehan'],
        ]);

        // Jika kode_register kosong → generate otomatis
        if (empty($barang->kode_register)) {
            $barang->kode_register = 'REG-' . str_pad((string)$barang->id, 6, '0', STR_PAD_LEFT);
            $barang->save();
        }

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', [
            'title' => 'Edit Barang',
            'barang' => $barang,
        ]);
    }

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
            'foto_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        /**
         * Cek lokasi manual di tabel locations
         */
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
            'kondisi'         => $validated['kondisi'] ?? 'Baik'
        ]);

        // Update special_code dan alternatif QR
        $barang->special_code = $barang->kode_barang . str_pad($barang->nup, 1, '0', STR_PAD_LEFT);
        $barang->alternatif_qr = trim(
            ($barang->kode_sakter ? $barang->kode_sakter : '') .
            $barang->kode_barang . str_pad($barang->nup, 1, '0', STR_PAD_LEFT),
            '-'
        );

        if (empty($barang->kode_register)) {
            $barang->kode_register = 'REG-' . str_pad((string)$barang->id, 6, '0', STR_PAD_LEFT);
        }
        // Jika ada file foto baru diupload
        if ($request->hasFile('foto_url')) {
            // Hapus foto lama jika ada
            if ($barang->foto_url) {
                Storage::delete('public/'.$barang->foto_url);
            }
            // Simpan foto baru
            $path = $request->file('foto_url')->store('barang', 'public');
            $barang->foto_url = $path;
        }

        $barang->save();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }

    public function qr($id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.show_qr', compact('barang'));
    }
}
