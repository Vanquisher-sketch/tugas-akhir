<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tanah;
use App\Models\Peralatan;
use App\Models\Gedung;
use App\Models\Jalan;
use App\Models\Bmd;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedLokasi = $request->input('lokasi');

        // --- 1. Ambil Semua Pilihan Lokasi untuk Dropdown ---
        // Menggunakan query builder murni agar proses load halaman lebih cepat
        $lokasiQuery = DB::table('tanahs')->select('lokasi')->whereNotNull('lokasi')->where('lokasi', '<>', '')->distinct()
            ->union(DB::table('peralatans')->select('lokasi')->whereNotNull('lokasi')->where('lokasi', '<>', '')->distinct())
            ->union(DB::table('gedungs')->select('lokasi')->whereNotNull('lokasi')->where('lokasi', '<>', '')->distinct())
            ->union(DB::table('jalans')->select('lokasi')->whereNotNull('lokasi')->where('lokasi', '<>', '')->distinct());
        
        $allLokasi = $lokasiQuery->pluck('lokasi')->filter()->unique()->values();

        // --- 2. Query Dasar Aset (Hanya Mengambil Data Aktif / Mengikuti Aturan Soft Deletes) ---
        $tanahQuery = Tanah::query();
        $peralatanQuery = Peralatan::query();
        $gedungQuery = Gedung::query();
        $jalanQuery = Jalan::query();
        
        // Menerapkan filter jika pengguna memilih wilayah tertentu
        if ($selectedLokasi) {
            $tanahQuery->where('lokasi', $selectedLokasi);
            $peralatanQuery->where('lokasi', $selectedLokasi);
            $gedungQuery->where('lokasi', $selectedLokasi);
            $jalanQuery->where('lokasi', $selectedLokasi);
        }

        // --- 3. Hitung Jumlah Unit Berkas (KPI) ---
        $countTanah = (clone $tanahQuery)->count();
        $countPeralatan = (clone $peralatanQuery)->count();
        $countGedung = (clone $gedungQuery)->count();
        $countJalan = (clone $jalanQuery)->count();
        
        // Menghitung Total Volume Manifes (KIB Global)
        $kpiTotalAset = $countTanah + $countPeralatan + $countGedung + $countJalan;

        // --- 4. Hitung Nilai Kapitalisasi Aset ---
        $nilaiTanah = (clone $tanahQuery)->sum('nilai_perolehan');
        $nilaiPeralatan = (clone $peralatanQuery)->sum('nilai_perolehan');
        $nilaiGedung = (clone $gedungQuery)->sum('nilai_perolehan');
        $nilaiJalan = (clone $jalanQuery)->sum('nilai_perolehan');
        
        $kpiTotalNilai = $nilaiTanah + $nilaiPeralatan + $nilaiGedung + $nilaiJalan;

        // --- 5. Monitoring Pajak Kendaraan Dinas (KIB B) ---
        $bmdData = Bmd::with('peralatan')
            ->when($selectedLokasi, function($query) use ($selectedLokasi) {
                return $query->where('lokasi', $selectedLokasi);
            })
            ->get();

        $now = Carbon::now();
        $tujuhHariLalu = Carbon::now()->subDays(7);
        $tigaPuluhHariSelesai = Carbon::now()->addDays(30);

        $asetPajakMendatang = $bmdData->filter(function ($item) use ($tujuhHariLalu, $tigaPuluhHariSelesai) {
            $kolomTanggal = $item->tgl_pajak ?? $item->tanggal_pajak ?? $item->jatuh_tempo ?? null;
            
            if (!$kolomTanggal) return false;

            $date = Carbon::parse($kolomTanggal);
            return $date->between($tujuhHariLalu, $tigaPuluhHariSelesai);
        })->sortBy(function ($item) {
            return $item->tgl_pajak ?? $item->tanggal_pajak ?? $item->jatuh_tempo;
        });

        // --- 6. Data Charts (Disiapkan untuk Chart.js / ApexCharts di Blade) ---
        $chartKomposisiAset = [
            'labels' => ['KIB A (Tanah)', 'KIB B (Peralatan)', 'KIB C (Gedung)', 'KIB D (Jalan)'],
            'data' => [$countTanah, $countPeralatan, $countGedung, $countJalan],
        ];

        $chartNilaiAset = [
            'labels' => ['KIB A', 'KIB B', 'KIB C', 'KIB D'],
            'data' => [$nilaiTanah, $nilaiPeralatan, $nilaiGedung, $nilaiJalan],
        ];

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