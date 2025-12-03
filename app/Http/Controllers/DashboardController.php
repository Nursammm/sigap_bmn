<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAset = Barang::count();
        $asetBaik = Barang::where('kondisi', 'Baik')->count();
        $asetRusakRingan = Barang::where('kondisi', 'Rusak Ringan')->count();
        $asetRusakBerat = Barang::where('kondisi', 'Rusak Berat')->count();
        $perluPerbaikan = $asetRusakRingan + $asetRusakBerat;
        $nilaiTotalAset = Barang::sum('nilai_perolehan');

        return view('dashboard', [
            'title' => 'Dashboard',
            'totalAset' => $totalAset,
            'asetBaik' => $asetBaik,
            'asetRusakRingan' => $asetRusakRingan,
            'asetRusakBerat' => $asetRusakBerat,
            'perluPerbaikan' => $perluPerbaikan,
            'nilaiTotalAset' => $nilaiTotalAset,
        ]);
    }
}
