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

        // --- 1. Ambil Semua Lokasi menggunakan DB murni tanpa campur tangan Model ---
        $lokasiQuery = DB::table('tanahs')->select('lokasi')->whereNotNull('lokasi')->distinct()
            ->union(DB::table('peralatans')->select('lokasi')->whereNotNull('lokasi')->distinct())
            ->union(DB::table('gedungs')->select('lokasi')->whereNotNull('lokasi')->distinct())
            ->union(DB::table('jalans')->select('lokasi')->whereNotNull('lokasi')->distinct());
        
        $allLokasi = $lokasiQuery->pluck('lokasi')->filter()->unique()->values();

        // --- 2. Query Dasar Aset (Ditambahkan withoutGlobalScopes agar abai terhadap SoftDeletes) ---
        $tanahQuery = Tanah::withoutGlobalScopes();
        $peralatanQuery = Peralatan::withoutGlobalScopes();
        $gedungQuery = Gedung::withoutGlobalScopes();
        $jalanQuery = Jalan::withoutGlobalScopes();
        
        if ($selectedLokasi) {
            $tanahQuery->where('lokasi', $selectedLokasi);
            $peralatanQuery->where('lokasi', $selectedLokasi);
            $gedungQuery->where('lokasi', $selectedLokasi);
            $jalanQuery->where('lokasi', $selectedLokasi);
        }

        // --- 3. Hitung KPI (Aman dari hantaman deleted_at) ---
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

        // --- 4. 🌟 REVISI AMAN: Ambil data BMD Kendaraan untuk Monitoring Pajak ---
        // Kita load data dasarnya dulu, lalu kita filter dinamis biar terhindar dari eror salah nama kolom SQL
        $bmdData = Bmd::with('peralatan')
            ->when($selectedLokasi, function($query) use ($selectedLokasi) {
                return $query->where('lokasi', $selectedLokasi);
            })
            ->get();

        $now = Carbon::now();
        $tujuhHariLalu = Carbon::now()->subDays(7);
        $tigaPuluhHariSelesai = Carbon::now()->addDays(30);

        // Filter data menggunakan Collection Laravel (Membaca dinamis properti model)
        $asetPajakMendatang = $bmdData->filter(function ($item) use ($tujuhHariLalu, $tigaPuluhHariSelesai) {
            // Cari properti tanggal yang ada di dalam model bmds (entah tgl_pajak, tanggal_pajak, atau jatuh_tempo)
            $kolomTanggal = $item->tgl_pajak ?? $item->tanggal_pajak ?? $item->jatuh_tempo ?? null;
            
            if (!$kolomTanggal) return false;

            $date = Carbon::parse($kolomTanggal);
            return $date->between($tujuhHariLalu, $tigaPuluhHariSelesai);
        })->sortBy(function ($item) {
            return $item->tgl_pajak ?? $item->tanggal_pajak ?? $item->jatuh_tempo;
        });

        // --- 5. Data Charts ---
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