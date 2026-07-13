<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tanah;
use App\Models\Peralatan;
use App\Models\Gedung;
use App\Models\Jalan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedLokasi = $request->input('lokasi');

        // --- 1. Ambil Semua Pilihan Lokasi untuk Dropdown ---
        // (Struktur tabel kita tetap mempertahankan kolom murni bernama 'lokasi' untuk ini)
        $lokasiQuery = DB::table('tanahs')->select('lokasi')->whereNotNull('lokasi')->where('lokasi', '<>', '')->distinct()
            ->union(DB::table('peralatans')->select('lokasi')->whereNotNull('lokasi')->where('lokasi', '<>', '')->distinct())
            ->union(DB::table('gedungs')->select('lokasi')->whereNotNull('lokasi')->where('lokasi', '<>', '')->distinct())
            ->union(DB::table('jalans')->select('lokasi')->whereNotNull('lokasi')->where('lokasi', '<>', '')->distinct());
        
        $allLokasi = $lokasiQuery->pluck('lokasi')->filter()->unique()->values();

        // --- 2. Query Dasar Aset ---
        $tanahQuery = Tanah::query();
        $peralatanQuery = Peralatan::query();
        $gedungQuery = Gedung::query();
        $jalanQuery = Jalan::query();
        
        // Filter lokasi
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
        
        $kpiTotalAset = $countTanah + $countPeralatan + $countGedung + $countJalan;

        // --- 4. Hitung Nilai Kapitalisasi Aset ---
        // 🌟 REVISI: Sesuaikan penjumlahan dengan prefix nama kolom database baru
        $nilaiTanah = (clone $tanahQuery)->sum('tanah_nilai_perolehan');
        $nilaiPeralatan = (clone $peralatanQuery)->sum('alat_nilai_perolehan');
        $nilaiGedung = (clone $gedungQuery)->sum('gedung_nilai_perolehan');
        $nilaiJalan = (clone $jalanQuery)->sum('jalan_nilai_perolehan');
        
        $kpiTotalNilai = $nilaiTanah + $nilaiPeralatan + $nilaiGedung + $nilaiJalan;

        // --- 5. Monitoring Pajak Kendaraan Dinas (SINKRONISASI DENGAN PAJAK CONTROLLER) ---
        $hariIni = Carbon::today();
        $batasPeringatan = Carbon::today()->addDays(30);

        // 🌟 REVISI: Sesuaikan pencarian radar pajak ke prefix nama kolom database baru
        $queryPajakMendatang = Peralatan::whereNotNull('alat_nomor_polisi')
            ->whereNotNull('alat_tanggal_pajak')
            ->where(function ($query) use ($hariIni, $batasPeringatan) {
                // Pajak Mati (Lewat jatuh tempo) atau Dekat Jatuh Tempo (< 30 hari)
                $query->where('alat_tanggal_pajak', '<=', $hariIni)
                      ->orWhereBetween('alat_tanggal_pajak', [$hariIni, $batasPeringatan]);
            });

        // Terapkan filter lokasi jika ada
        if ($selectedLokasi) {
            $queryPajakMendatang->where('lokasi', $selectedLokasi);
        }

        // Ambil data dan urutkan dari yang paling kritis
        $asetPajakMendatang = $queryPajakMendatang->orderBy('alat_tanggal_pajak', 'asc')->get();

        // --- 6. Data Charts ---
        $chartKomposisiAset = [
            'labels' => ['KIB A (Tanah)', 'KIB B (Peralatan)', 'KIB C (Gedung)', 'KIB D (Jalan)'],
            'data' => [$countTanah, $countPeralatan, $countGedung, $countJalan],
        ];

        $chartNilaiAset = [
            'labels' => ['KIB A', 'KIB B', 'KIB C', 'KIB D'],
            'data' => [$nilaiTanah, $nilaiPeralatan, $nilaiGedung, $nilaiJalan],
        ];

        return view('pages.dashboard', compact(
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