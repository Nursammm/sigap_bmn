<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\MutasiBarang;

class MutasiController extends Controller
{
    public function index()
    {
        $mutasi = MutasiBarang::with('barang', 'fromSakter', 'toSakter')->get();
        return view('mutasi.index', compact('mutasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'to_sakter_id' => 'required|exists:kode_sakter,id',
        ]);

        $barang = Barang::findOrFail($request->barang_id);
        $mutasi = MutasiBarang::create([
            'barang_id' => $barang->id,
            'from_sakter_id' => $barang->kode_sakter_id,
            'to_sakter_id' => $request->to_sakter_id,
            'tanggal' => now(),
        ]);

        $barang->update(['kode_sakter_id' => $request->to_sakter_id]);

        return redirect()->route('mutasi.index')->with('success', 'Mutasi berhasil.');
    }
}
