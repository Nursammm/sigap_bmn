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
    public function index(Request $request)
    {
        $q       = trim((string) $request->query('q', ''));
        $kondisi = $request->query('kondisi');
        $sort    = $request->query('sort');

        $query = Barang::with('location');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('nama_barang', 'like', "%{$q}%")
                   ->orWhere('nup', 'like', "%{$q}%")
                   ->orWhere('kode_barang', 'like', "%{$q}%")
                   ->orWhere('kode_register', 'like', "%{$q}%")
                   ->orWhere('sn', 'like', "%{$q}%")
                   ->orWhere('merek', 'like', "%{$q}%")
                   ->orWhereHas('location', function ($qLoc) use ($q) {
                       $qLoc->where('name', 'like', "%{$q}%");
                   });
            });
        }

        if (!empty($kondisi)) {
            $query->where('kondisi', $kondisi);
        }

        switch ($sort) {
            case 'asc': 
                $query->orderBy('nama_barang', 'asc');
                break;
            case 'desc':      
                $query->orderBy('nama_barang', 'desc');
                break;
            case 'nilai_asc':   
                $query->orderBy('nilai_perolehan', 'asc');
                break;
            case 'nilai_desc':  
                $query->orderBy('nilai_perolehan', 'desc');
                break;
            case 'tanggal_asc': 
                $query->orderBy('tgl_perolehan', 'asc');
                break;
            case 'tanggal_desc': 
                $query->orderBy('tgl_perolehan', 'desc');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $barangs = $query->paginate(25)->appends(request()->query());

        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        $locations = Location::orderBy('name')->get(['id', 'name']);

        $nameCodeMap = Barang::select('nama_barang', 'kode_barang')
            ->whereNotNull('nama_barang')
            ->whereNotNull('kode_barang')
            ->distinct()
            ->orderBy('nama_barang')
            ->get()
            ->pluck('kode_barang', 'nama_barang');

        return view('barang.create', [
            'title'        => 'Tambah Barang',
            'locations'    => $locations,
            'nameCodeMap'  => $nameCodeMap,
        ]);
    }

    public function show(Barang $barang)
    {
        return view('barang.show', compact('barang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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
                $locationId = null;
            }
        }

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

        $kodeSakter = $validated['kode_sakter']
            ?? Barang::where('nama_barang', $validated['nama_barang'])
                ->whereNotNull('kode_sakter')
                ->orderByDesc('id')
                ->value('kode_sakter')
            ?? $kodeBarang;

        $latestNup = Barang::where('kode_barang', $kodeBarang)->max('nup') ?? 0;
        $nup       = $latestNup + 1;

        $specialCode = $kodeBarang . str_pad($nup, 1, '0', STR_PAD_LEFT);

        $gambarPath = [];
        if ($request->hasFile('foto_url')) {
            foreach ($request->file('foto_url') as $file) {
                $gambarPath[] = $file->store('barang', 'public');
            }
        }

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
            'kode_sakter'     => $kodeSakter,
            'sn'              => $validated['sn'],
            'qr_string'       => $validated['kode_register'] ?? null,
            'alternatif_qr'   => ($kodeSakter ?? '') . '*' . $kodeBarang . '*' . $nup,
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
            'foto_url'        => 'nullable|array',
            'foto_url.*'      => 'image|mimes:jpg,jpeg,png|max:2048',

            'existing_photos' => 'nullable|array',
            'remove_photos'   => 'nullable|array',
            'remove_photos.*' => 'string',
        ]);

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

        $barang->special_code = $barang->kode_barang . str_pad($barang->nup, 1, '0', STR_PAD_LEFT);
        $barang->alternatif_qr = ($barang->kode_sakter ?? '') . '*' .
            $barang->kode_barang . '*' . str_pad($barang->nup, 1, '0', STR_PAD_LEFT);

        $currentPhotos = $barang->foto_url ?? [];
        if (!is_array($currentPhotos)) {
            $currentPhotos = $currentPhotos ? [$currentPhotos] : [];
        }

        $remove = $request->input('remove_photos', []);
        if (!empty($remove)) {
            foreach ($remove as $path) {
                if (in_array($path, $currentPhotos, true)) {
                    Storage::disk('public')->delete($path);
                }
            }
            $currentPhotos = array_values(array_diff($currentPhotos, $remove));
        }

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
