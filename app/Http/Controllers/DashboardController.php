<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tanah;
use App\Models\Peralatan;
use App\Models\Gedung;
use App\Models\Jalan;
use App\Models\Bmd; // Pastikan Model Bmd diimport
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedLokasi = $request->input('lokasi');

        // --- 1. Ambil Semua Lokasi untuk Filter ---
        $lokasiQuery = Tanah::select('lokasi')->distinct()
            ->union(Peralatan::select('lokasi')->distinct())
            ->union(Gedung::select('lokasi')->distinct())
            ->union(Jalan::select('lokasi')->distinct());
        
        $allLokasi = $lokasiQuery->pluck('lokasi')->filter();

        // --- 2. Query Dasar Aset ---
        $tanahQuery = Tanah::query();
        $peralatanQuery = Peralatan::query();
        $gedungQuery = Gedung::query();
        $jalanQuery = Jalan::query();
        
        if ($selectedLokasi) {
            $tanahQuery->where('lokasi', $selectedLokasi);
            $peralatanQuery->where('lokasi', $selectedLokasi);
            $gedungQuery->where('lokasi', $selectedLokasi);
            $jalanQuery->where('lokasi', $selectedLokasi);
        }

        // --- 3. Hitung KPI ---
        $nilaiTanah = (clone $tanahQuery)->sum('nilai_perolehan');
        $nilaiPeralatan = (clone $peralatanQuery)->sum('nilai_perolehan');
        $nilaiGedung = (clone $gedungQuery)->sum('nilai_perolehan');
        $nilaiJalan = (clone $jalanQuery)->sum('nilai_perolehan');
        
        $kpiTotalNilai = $nilaiTanah + $nilaiPeralatan + $nilaiGedung + $nilaiJalan;

        $countTanah = (clone $tanahQuery)->count();
        $countPeralatan = (clone $peralatanQuery)->count();
        $countGedung = (clone $gedungQuery)->count();
        $countJalan = (clone $jalanQuery)->count();
        
        $kpiTotalAset = $countTanah + $countPeralatan + $countGedung + $countJalan;

        // --- 4. Monitoring Pajak (DARI TABEL BMDS) ---
        // Kita mengambil data dari tabel bmds dan join ke peralatans untuk nama barangnya
        $asetPajakMendatang = Bmd::with('peralatan') // Asumsi ada relasi 'peralatan' di model Bmd
            ->whereNotNull('tanggal_pajak')
            ->whereBetween('tanggal_pajak', [
                Carbon::now()->subDays(7), 
                Carbon::now()->addDays(30)
            ])
            ->when($selectedLokasi, function($query) use ($selectedLokasi) {
                return $query->where('lokasi', $selectedLokasi);
            })
            ->orderBy('tanggal_pajak', 'asc')
            ->get();

        // --- 5. Data Charts ---
        $chartKomposisiAset = [
            'labels' => ['KIB A', 'KIB B', 'KIB C', 'KIB D'],
            'data' => [$countTanah, $countPeralatan, $countGedung, $countJalan],
        ];

        $chartNilaiAset = [
            'labels' => ['KIB A', 'KIB B', 'KIB C', 'KIB D'],
            'data' => [$nilaiTanah, $nilaiPeralatan, $nilaiGedung, $nilaiJalan],
        ];

        // Chart Perolehan... (logika sama seperti sebelumnya)
        $allPerolehan = []; // ... logic skip for brevity ...

        return view('pages/dashboard', compact(
            'kpiTotalNilai', 
            'kpiTotalAset', 
            'asetPajakMendatang',
            'chartKomposisiAset',
            'chartNilaiAset',
            'allLokasi',
            'selectedLokasi',
            'countTanah',
            'countPeralatan',
            'countGedung',
            'countJalan'
        ));
    }
}