<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Barang, Location};
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\BarangExport;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $lokasiId = (int) $request->query('lokasi');
        $search   = trim((string) $request->query('q', ''));
        $kondisi  = $request->query('kondisi');
        $sort     = $request->query('sort');

        // Daftar ruangan + jumlah barang
        $locations = Location::withCount('barangs')
            ->orderBy('name')
            ->get(['id','name']);

        // Query barang di ruangan terpilih
        $base = Barang::query()
            ->with(['category','location'])
            ->withCount([
                'maintenances as open_maintenance_count' => fn($q) => $q->open()
            ])
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
                fn($q) => $q->where('kondisi', $kondisi));

        // Urutan / sort
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
                $base->latest('tgl_perolehan'); // default sama seperti sebelumnya
        }

        // Paginasi
        $barangs = $base->paginate(15)->appends($request->query());

        // Statistik kondisi untuk ruangan terpilih
        $stats = [
            'total'  => (clone $base)->count(),
            'baik'   => (clone $base)->where('kondisi','Baik')->count(),
            'rr'     => (clone $base)->where('kondisi','Rusak Ringan')->count(),
            'rb'     => (clone $base)->where('kondisi','Rusak Berat')->count(),
            'hilang' => (clone $base)->where('kondisi','Hilang')->count(),
        ];

        // Ruangan aktif (opsional)
        $activeLocation = $lokasiId ? $locations->firstWhere('id', $lokasiId) : null;

        return view('ruangan.index', compact(
            'locations','barangs','stats','activeLocation',
            'lokasiId','search','kondisi'
        ));
    }

    public function print(Request $request)
    {
        $lokasiId = (int) $request->query('lokasi');
        $search   = trim((string) $request->query('q', ''));
        $kondisi  = $request->query('kondisi');
        $sort     = $request->query('sort');

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
                fn($q) => $q->where('kondisi', $kondisi));

        // Urutan untuk tampilan print juga ikut filter sort
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

        // Tanpa pagination untuk cetak
        $items = $base->get();

        $location = $lokasiId ? Location::find($lokasiId) : null;

        return view('ruangan.print', compact('items','location','search','kondisi'));
    }

    /** EXPORT EXCEL SESUAI FILTER */


    /** EXPORT PDF SESUAI FILTER (GUNAKAN VIEW ruangan.print) */
    public function exportPdf(Request $request)
    {
        $lokasiId = (int) $request->query('lokasi');
        $search   = trim((string) $request->query('q', ''));
        $kondisi  = $request->query('kondisi');
        $sort     = $request->query('sort');

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

        $items    = $base->get();
        $location = $lokasiId ? Location::find($lokasiId) : null;

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
    $lokasiId = (int) $request->query('lokasi');
    $search   = trim((string) $request->query('q', ''));
    $kondisi  = $request->query('kondisi');
    $sort     = $request->query('sort');

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

    // PAKAI .csv, BUKAN .xls
    $filename = 'barang_ruangan_' . now()->format('Ymd_His') . '.csv';

    $headers = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    $callback = function () use ($items) {
        $handle = fopen('php://output', 'w');

        // header kolom
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
        ], ';'); // ; supaya enak di regional Indonesia

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
