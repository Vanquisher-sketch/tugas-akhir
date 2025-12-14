<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tanah;
use App\Models\Peralatan;
use App\Models\Gedung;
use App\Models\Jalan;
// Model Inventaris dan Room tidak digunakan di sini
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data agregat (HANYA KIB A, B, C, D).
     */
    public function index(Request $request)
    {
        $selectedLokasi = $request->input('lokasi');

        // --- 1. Ambil Semua Lokasi untuk Filter (Hanya KIB A-D) ---
        $lokasiQuery = Tanah::select('lokasi')->distinct()
            ->union(Peralatan::select('lokasi')->distinct())
            ->union(Gedung::select('lokasi')->distinct())
            ->union(Jalan::select('lokasi')->distinct());
        
        $allLokasi = $lokasiQuery->pluck('lokasi')->filter(); // ->filter() untuk hapus null/empty

        // --- 2. Siapkan Query Dasar dengan Filter Lokasi ---
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

        // --- 3. Hitung KPI (Key Performance Indicators) ---

        // KPI 1: Total Nilai Aset (KIB A-D)
        // Menggunakan asumsi nama kolom dari revisi sebelumnya ('nilai_perolehan')
        $nilaiTanah = (clone $tanahQuery)->sum('nilai_perolehan');
        $nilaiPeralatan = (clone $peralatanQuery)->sum('nilai_perolehan');
        $nilaiGedung = (clone $gedungQuery)->sum('nilai_perolehan');
        $nilaiJalan = (clone $jalanQuery)->sum('nilai_perolehan');
        
        $kpiTotalNilai = $nilaiTanah + $nilaiPeralatan + $nilaiGedung + $nilaiJalan;

        // KPI 2: Total Aset Terdaftar (KIB A-D)
        $countTanah = (clone $tanahQuery)->count();
        $countPeralatan = (clone $peralatanQuery)->count();
        $countGedung = (clone $gedungQuery)->count();
        $countJalan = (clone $jalanQuery)->count();
        
        $kpiTotalAset = $countTanah + $countPeralatan + $countGedung + $countJalan;


        // --- 4. Siapkan Data untuk Charts ---

        // Chart 1: Komposisi Aset (Pie)
        $chartKomposisiAset = [
            'labels' => ['KIB A (Tanah)', 'KIB B (Peralatan)', 'KIB C (Gedung)', 'KIB D (Jalan)'],
            'data' => [$countTanah, $countPeralatan, $countGedung, $countJalan],
        ];

        // Chart 2: Nilai Aset per KIB (Bar)
        $chartNilaiAset = [
            'labels' => ['KIB A', 'KIB B', 'KIB C', 'KIB D'],
            'data' => [$nilaiTanah, $nilaiPeralatan, $nilaiGedung, $nilaiJalan],
        ];

        // Chart 3: Perolehan Aset per Tahun (Line)
        $perolehanA = (clone $tanahQuery)->whereNotNull('tanggal_perolehan')->select(DB::raw('YEAR(tanggal_perolehan) as tahun'), DB::raw('count(*) as jumlah'))->groupBy('tahun')->pluck('jumlah', 'tahun');
        $perolehanB = (clone $peralatanQuery)->whereNotNull('tanggal_perolehan')->select(DB::raw('YEAR(tanggal_perolehan) as tahun'), DB::raw('count(*) as jumlah'))->groupBy('tahun')->pluck('jumlah', 'tahun');
        $perolehanC = (clone $gedungQuery)->whereNotNull('tanggal_perolehan')->select(DB::raw('YEAR(tanggal_perolehan) as tahun'), DB::raw('count(*) as jumlah'))->groupBy('tahun')->pluck('jumlah', 'tahun');
        $perolehanD = (clone $jalanQuery)->whereNotNull('tanggal_perolehan')->select(DB::raw('YEAR(tanggal_perolehan) as tahun'), DB::raw('count(*) as jumlah'))->groupBy('tahun')->pluck('jumlah', 'tahun');
        
        $allPerolehan = [];
        foreach ([$perolehanA, $perolehanB, $perolehanC, $perolehanD] as $collection) {
            foreach ($collection as $tahun => $jumlah) {
                if ($tahun) {
                    $allPerolehan[$tahun] = ($allPerolehan[$tahun] ?? 0) + $jumlah;
                }
            }
        }
        ksort($allPerolehan); // Urutkan berdasarkan tahun

        $chartPerolehan = [
            'labels' => array_keys($allPerolehan),
            'data' => array_values($allPerolehan),
        ];

        // --- 5. Siapkan Quick Lists (Dihapus) ---

        // --- 6. Kirim ke View ---
        return view('pages/dashboard', compact(
            'kpiTotalNilai', 
            'kpiTotalAset', 
            'chartKomposisiAset',
            'chartNilaiAset',
            'chartPerolehan',
            'allLokasi',
            'selectedLokasi',
            // Variabel count untuk navigasi
            'countTanah',
            'countPeralatan',
            'countGedung',
            'countJalan'
        ));
    }
}