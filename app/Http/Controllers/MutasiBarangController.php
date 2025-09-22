<?php

namespace App\Http\Controllers;

use App\Models\{Barang, Location, MutasiBarang};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MutasiBarangController extends Controller
{
    // Form mutasi untuk 1 barang
    public function create(Barang $barang)
    {
        $this->middleware('auth');

        return view('mutasi.create', [
            'barang'    => $barang->load('location'),
            'locations' => Location::orderBy('name')->get(),
        ]);
    }

    // Simpan mutasi dan update lokasi barang
    public function store(Request $request, Barang $barang)
{
    $request->merge(['from_location_id' => $barang->location_id]);

    $data = $request->validate([
        'lokasi'            => ['required','string','max:255'],
        'tanggal'           => ['required','date'],
        'catatan'           => ['nullable','string','max:1000'],
        'from_location_id'  => ['nullable','exists:locations,id'],
    ],[
        'lokasi.required'   => 'Lokasi tujuan wajib diisi.',
    ]);

    // Temukan atau buat lokasi dari nama yang diketik/dipilih
    $lokasiNama = trim($data['lokasi']);
    $toLocation = Location::firstOrCreate(['name' => $lokasiNama]);

    // Cegah mutasi ke lokasi yang sama
    if ((int) $toLocation->id === (int) $barang->location_id) {
        return back()
            ->withErrors(['lokasi' => 'Lokasi tujuan tidak boleh sama dengan lokasi saat ini.'])
            ->withInput();
    }

    // Simpan mutasi
    $mutasi = MutasiBarang::create([
        'barang_id'        => $barang->id,
        'from_location_id' => $data['from_location_id'] ?? null,
        'to_location_id'   => $toLocation->id,
        'moved_by'         => Auth::id() ?? $request->user()?->id,
        'tanggal'          => $data['tanggal'],
        'catatan'          => $data['catatan'] ?? null,
    ]);

    // Update lokasi aktif barang
    $barang->update(['location_id' => $toLocation->id]);

    return redirect()->route('barang.index')->with('success','Mutasi lokasi tersimpan.');
}

    // (Opsional) daftar riwayat mutasi semua barang
    public function index()
{
    $items = \App\Models\MutasiBarang::with(['barang','from','to','mover'])
        ->latest('tanggal')->latest()
        ->paginate(20);

    return view('mutasi.index', compact('items'));
    $totalBarangs = \App\Models\Barang::count();
    return view('mutasi.index', compact('items','totalBarangs'));

}

public function __construct()
    {
        $this->middleware('auth'); // jangan ditaruh di dalam method
    }

}
