<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Barang, Location};
use Barryvdh\DomPDF\Facade\Pdf;

class RuanganController extends Controller
{
    /**
     * Normalisasi parameter lokasi dari request.
     *
     * @return array [$lokasiParam, $lokasiId, $lokasiFilterId]
     *
     *  - $lokasiParam   : string yang dipakai untuk dibawa ke view ('' | '-' | '12' ...)
     *  - $lokasiId      : integer ID lokasi jika numerik, selain itu null
     *  - $lokasiFilterId: 0 untuk '-', >0 untuk lokasi tertentu, null jika semua
     */
    protected function resolveLokasiFilter(Request $request): array
    {
        $lokasiParamRaw = (string) $request->query('lokasi', '');

        // Jika ada request dengan '0', anggap sama dengan '-'
        if ($lokasiParamRaw === '0') {
            $lokasiParamRaw = '-';
        }

        // Hanya izinkan '', '-', atau numerik
        $lokasiParam = ($lokasiParamRaw === '' || $lokasiParamRaw === '-' || is_numeric($lokasiParamRaw))
            ? $lokasiParamRaw
            : '';

        $lokasiId = is_numeric($lokasiParam) ? (int) $lokasiParam : null;

        // Khusus '-' kita pakai sentinel 0 untuk "tanpa ruangan"
        $lokasiFilterId = ($lokasiParam === '-') ? 0 : ($lokasiId ?? null);

        return [$lokasiParam, $lokasiId, $lokasiFilterId];
    }

    public function index(Request $request)
    {
        [$lokasiParam, $lokasiId, $lokasiFilterId] = $this->resolveLokasiFilter($request);

        $search  = trim((string) $request->query('q', ''));
        $kondisi = $request->query('kondisi');
        $sort    = $request->query('sort');

        // Daftar lokasi normal (id > 0)
        $locations = Location::where('id', '>', 0)
            ->withCount('barangs')
            ->orderBy('name')
            ->get(['id','name']);

        $base = Barang::query()
            ->with(['category','location'])
            ->withCount([
                'maintenances as open_maintenance_count' => fn($q) => $q->open()
            ])

            // Filter lokasi: '-' => barang tanpa ruangan (location_id 0 atau NULL)
            ->when($lokasiFilterId === 0, function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNull('location_id')
                       ->orWhere('location_id', 0);
                });
            })
            // Lokasi normal
            ->when($lokasiFilterId !== null && $lokasiFilterId > 0, fn($q) => $q->where('location_id', $lokasiFilterId))

            // Filter pencarian
            ->when($search !== '', function ($q) use ($search) {
                $s = "%{$search}%";
                $q->where(function($w) use ($s) {
                    $w->where('nama_barang', 'like', $s)
                      ->orWhere('kode_register', 'like', $s)
                      ->orWhere('kode_barang', 'like', $s)
                      ->orWhere('merek', 'like', $s);
                });
            })

            // Filter kondisi
            ->when(
                in_array($kondisi, ['Baik','Rusak Ringan','Rusak Berat','Hilang'], true),
                fn($q) => $q->where('kondisi', $kondisi)
            );

        // Sorting
        switch ($sort) {
            case 'tgl_perolehan_asc':
                $base->orderBy('tgl_perolehan', 'asc');
                break;
            case 'tgl_perolehan_desc':
                $base->orderBy('tgl_perolehan', 'desc');
                break;
            case 'nama_asc':
                $base->orderBy('nama_barang', 'asc');
                break;
            case 'nama_desc':
                $base->orderBy('nama_barang', 'desc');
                break;
            case 'kode_asc':
                $base->orderBy('kode_barang', 'asc');
                break;
            case 'kode_desc':
                $base->orderBy('kode_barang', 'desc');
                break;
            case 'input_asc':
                $base->orderBy('created_at', 'asc');
                break;
            case 'input_desc':
                $base->orderBy('created_at', 'desc');
                break;
            default:
                $base->latest('tgl_perolehan');
        }

        $barangs = $base->paginate(15)->appends($request->query());

        $stats = [
            'total'  => (clone $base)->count(),
            'baik'   => (clone $base)->where('kondisi','Baik')->count(),
            'rr'     => (clone $base)->where('kondisi','Rusak Ringan')->count(),
            'rb'     => (clone $base)->where('kondisi','Rusak Berat')->count(),
            'hilang' => (clone $base)->where('kondisi','Hilang')->count(),
        ];

        // Active location hanya untuk lokasi yang benar2 ada (id > 0)
        $activeLocation = ($lokasiFilterId !== null && $lokasiFilterId > 0)
            ? $locations->firstWhere('id', $lokasiFilterId)
            : null;

        return view('ruangan.index', compact(
            'locations','barangs','stats','activeLocation',
            'lokasiParam','lokasiId','search','kondisi'
        ));
    }

    public function print(Request $request)
    {
        [$lokasiParam, $lokasiId, $lokasiFilterId] = $this->resolveLokasiFilter($request);

        $search  = trim((string) $request->query('q', ''));
        $kondisi = $request->query('kondisi');
        $sort    = $request->query('sort');

        $base = Barang::query()
            ->with(['category','location'])
            ->when($lokasiFilterId === 0, function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNull('location_id')
                       ->orWhere('location_id', 0);
                });
            })
            ->when($lokasiFilterId !== null && $lokasiFilterId > 0, fn($q) => $q->where('location_id', $lokasiFilterId))
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
                fn($q) => $q->where('kondisi', $kondisi));

        switch ($sort) {
            case 'tgl_perolehan_asc':
                $base->orderBy('tgl_perolehan', 'asc');
                break;
            case 'tgl_perolehan_desc':
                $base->orderBy('tgl_perolehan', 'desc');
                break;
            case 'nama_asc':
                $base->orderBy('nama_barang', 'asc');
                break;
            case 'nama_desc':
                $base->orderBy('nama_barang', 'desc');
                break;
            case 'kode_asc':
                $base->orderBy('kode_barang', 'asc');
                break;
            case 'kode_desc':
                $base->orderBy('kode_barang', 'desc');
                break;
            case 'input_asc':
                $base->orderBy('created_at', 'asc');
                break;
            case 'input_desc':
                $base->orderBy('created_at', 'desc');
                break;
            default:
                $base->orderBy('nama_barang');
        }

        $items = $base->get();

        $location = ($lokasiParam === '-')
            ? null
            : ($lokasiFilterId ? Location::find($lokasiFilterId) : null);

        return view('ruangan.print', compact('items','location','search','kondisi'));
    }

    public function exportPdf(Request $request)
    {
        [$lokasiParam, $lokasiId, $lokasiFilterId] = $this->resolveLokasiFilter($request);

        $search  = trim((string) $request->query('q', ''));
        $kondisi = $request->query('kondisi');
        $sort    = $request->query('sort');

        $base = Barang::query()
            ->with(['category','location'])
            ->when($lokasiFilterId === 0, function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNull('location_id')
                       ->orWhere('location_id', 0);
                });
            })
            ->when($lokasiFilterId !== null && $lokasiFilterId > 0, fn($q) => $q->where('location_id', $lokasiFilterId))
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
                fn($q) => $q->where('kondisi', $kondisi));

        switch ($sort) {
            case 'tgl_perolehan_asc':
                $base->orderBy('tgl_perolehan', 'asc');
                break;
            case 'tgl_perolehan_desc':
                $base->orderBy('tgl_perolehan', 'desc');
                break;
            case 'nama_asc':
                $base->orderBy('nama_barang', 'asc');
                break;
            case 'nama_desc':
                $base->orderBy('nama_barang', 'desc');
                break;
            case 'kode_asc':
                $base->orderBy('kode_barang', 'asc');
                break;
            case 'kode_desc':
                $base->orderBy('kode_barang', 'desc');
                break;
            case 'input_asc':
                $base->orderBy('created_at', 'asc');
                break;
            case 'input_desc':
                $base->orderBy('created_at', 'desc');
                break;
            default:
                $base->orderBy('nama_barang');
        }

        $items = $base->get();
        $location = ($lokasiParam === '-')
            ? null
            : ($lokasiFilterId ? Location::find($lokasiFilterId) : null);

        $pdf = PDF::loadView('ruangan.print', [
            'items'    => $items,
            'location' => $location,
            'search'   => $search,
            'kondisi'  => $kondisi,
        ])->setPaper('A4', 'portrait');

        $filename = 'barang_ruangan_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        [$lokasiParam, $lokasiId, $lokasiFilterId] = $this->resolveLokasiFilter($request);

        $search  = trim((string) $request->query('q', ''));
        $kondisi = $request->query('kondisi');
        $sort    = $request->query('sort');

        $base = Barang::query()
            ->with(['category','location'])
            ->when($lokasiFilterId === 0, function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNull('location_id')
                       ->orWhere('location_id', 0);
                });
            })
            ->when($lokasiFilterId !== null && $lokasiFilterId > 0, fn($q) => $q->where('location_id', $lokasiFilterId))
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
                fn($q) => $q->where('kondisi', $kondisi));

        switch ($sort) {
            case 'tgl_perolehan_asc':
                $base->orderBy('tgl_perolehan', 'asc'); break;
            case 'tgl_perolehan_desc':
                $base->orderBy('tgl_perolehan', 'desc'); break;
            case 'nama_asc':
                $base->orderBy('nama_barang', 'asc'); break;
            case 'nama_desc':
                $base->orderBy('nama_barang', 'desc'); break;
            case 'kode_asc':
                $base->orderBy('kode_barang', 'asc'); break;
            case 'kode_desc':
                $base->orderBy('kode_barang', 'desc'); break;
            case 'input_asc':
                $base->orderBy('created_at', 'asc'); break;
            case 'input_desc':
                $base->orderBy('created_at', 'desc'); break;
            default:
                $base->orderBy('nama_barang');
        }

        $items = $base->get();

        $filename = 'barang_ruangan_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($items) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Kode Register',
                'Nama Barang',
                'Kode Barang',
                'NUP',
                'Merek',
                'Kondisi',
                'Tanggal Perolehan',
                'Nilai Perolehan',
                'Ruangan',
            ], ';');

            foreach ($items as $b) {
                fputcsv($handle, [
                    $b->kode_register,
                    $b->nama_barang,
                    $b->kode_barang,
                    $b->nup,
                    $b->merek,
                    $b->kondisi,
                    $b->tgl_perolehan,
                    $b->nilai_perolehan,
                    optional($b->location)->name,
                ], ';');
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    public function show(Location $location)
    {
        $barangs = Barang::with('location')
            ->where('location_id', $location->id)
            ->latest('tgl_perolehan')
            ->paginate(20);

        return view('ruangan.show', compact('location','barangs'));
    }
}
