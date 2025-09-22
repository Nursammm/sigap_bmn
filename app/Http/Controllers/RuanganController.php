<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Barang, Location};

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $lokasiId = (int) $request->query('lokasi');
        $search   = trim((string) $request->query('q', ''));
        $kondisi  = $request->query('kondisi');

        // Daftar ruangan + jumlah barang
        $locations = Location::withCount('barangs')
            ->orderBy('name')
            ->get(['id','name']);

        // Query barang di ruangan terpilih
        $base = Barang::query()
            ->with(['category','location'])
            ->when($lokasiId > 0, fn($q) => $q->where('location_id', $lokasiId))
            ->when($search !== '', function ($q) use ($search) {
                $s = "%{$search}%";
                $q->where(function($w) use ($s) {
                    $w->where('nama_barang', 'like', $s)
                      ->orWhere('kode_register', 'like', $s)
                      ->orWhere('kode_barang', 'like', $s)
                      ->orWhere('merek', 'like', $s);
                });
            })
            ->when(in_array($kondisi, ['Baik','Rusak Ringan','Rusak Berat','Hilang'], true),
                fn($q) => $q->where('kondisi', $kondisi))
            ->latest('tgl_perolehan');

        $barangs = $base->paginate(15)->appends($request->query());

        // Statistik kondisi untuk ruangan terpilih
        $stats = [
            'total'        => (clone $base)->count(),
            'baik'         => (clone $base)->where('kondisi','Baik')->count(),
            'rr'           => (clone $base)->where('kondisi','Rusak Ringan')->count(),
            'rb'           => (clone $base)->where('kondisi','Rusak Berat')->count(),
            'hilang'       => (clone $base)->where('kondisi','Hilang')->count(),
        ];

        // Ruangan aktif (opsional)
        $activeLocation = $lokasiId ? $locations->firstWhere('id', $lokasiId) : null;

        return view('ruangan.index', compact('locations','barangs','stats','activeLocation','lokasiId','search','kondisi'));
    }

    public function print(Request $request)
    {
        $lokasiId = (int) $request->query('lokasi');
        $search   = trim((string) $request->query('q', ''));
        $kondisi  = $request->query('kondisi');

        $base = Barang::query()
            ->with(['category','location'])
            ->when($lokasiId > 0, fn($q) => $q->where('location_id', $lokasiId))
            ->when($search !== '', function ($q) use ($search) {
                $s = "%{$search}%";
                $q->where(function($w) use ($s) {
                    $w->where('nama_barang', 'like', $s)
                      ->orWhere('kode_register', 'like', $s)
                      ->orWhere('kode_barang', 'like', $s)
                      ->orWhere('merek', 'like', $s);
                });
            })
            ->when(in_array($kondisi, ['Baik','Rusak Ringan','Rusak Berat','Hilang'], true),
                fn($q) => $q->where('kondisi', $kondisi))
            ->orderBy('nama_barang');

        // Tanpa pagination untuk cetak
        $items = $base->get();

        $location = $lokasiId ? Location::find($lokasiId) : null;

        return view('ruangan.print', compact('items','location','search','kondisi'));
    }
}
