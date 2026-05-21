<?php

namespace App\Http\Controllers;

use App\Models\Peralatan;
use App\Models\Bmd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PajakController extends Controller
{
    /**
     * MENAMPILKAN DATA (INDEX) - OTOMATIS PATROLI REAL-TIME 🌟
     * Menampilkan daftar kendaraan yang pajaknya sudah dekat tempo (< 30 hari) atau sudah lewat.
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        $hariIni = Carbon::today();
        $batasPeringatan = Carbon::today()->addDays(30);

        // Ambil data kendaraan dari tabel peralatans yang pajaknya kritis
        $pajaks = Peralatan::where('lokasi', $lokasi)
            ->whereNotNull('nomor_polisi')
            ->whereNotNull('tanggal_pajak')
            ->where(function ($query) use ($hariIni, $batasPeringatan) {
                // Kondisi 1: Sudah lewat jatuh tempo (Pajak Mati)
                $query->where('tanggal_pajak', '<=', $hariIni)
                // Kondisi 2: Dekat jatuh tempo (Kurang dari atau sama dengan 30 hari lagi)
                      ->orWhereBetween('tanggal_pajak', [$hariIni, $batasPeringatan]);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_barang', 'LIKE', "%{$search}%")
                      ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                      ->orWhere('nomor_polisi', 'LIKE', "%{$search}%")
                      ->orWhere('merk_tipe', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('tanggal_pajak', 'asc') // Urutkan dari yang paling darurat / kritis duluan
            ->paginate(10)
            ->withQueryString(); 
        
        return view("pages.{$lokasi}.pajak.index", compact('pajaks', 'lokasi', 'search'));
    }

    /**
     * FORM EDIT (Update Tanggal Pajak Langsung ke Aset Peralatan)
     */
    public function edit($lokasi, $id)
    {
        // $id di sini adalah kode_barang milik Peralatan
        $pajak = Peralatan::where('lokasi', $lokasi)
                          ->findOrFail($id);

        return view("pages.{$lokasi}.pajak.edit", compact('pajak', 'lokasi'));
    }

    /**
     * PROSES UPDATE (Sinkronisasi Data Legalitas Pajak Terbaru)
     */
    public function update(Request $request, $lokasi, $id)
    {
        // 1. Validasi Input Tanggal Pajak Baru
        $validated = $request->validate([
            'tanggal_pajak'   => 'required|date', // Pajak Tahunan wajib diupdate
            'tanggal_stnk'    => 'nullable|date', // Pajak 5 Tahunan
        ]);

        // 2. Ambil data aset Peralatan
        $peralatan = Peralatan::where('lokasi', $lokasi)->findOrFail($id);

        DB::beginTransaction();
        try {
            // 3. Update data legalitas langsung di tabel peralatans
            $peralatan->update([
                'tanggal_pajak'   => $validated['tanggal_pajak'],
                'tanggal_stnk'    => $validated['tanggal_stnk'] ?? $peralatan->tanggal_stnk,
            ]);

            DB::commit();

            return redirect()->route('lokasi.pajak.index', $lokasi)
                             ->with('success', 'Data tanggal perpanjangan Pajak dan STNK kendaraan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Update Pajak Peralatan: " . $e->getMessage());
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal memperbarui data tanggal pajak kendaraan.');
        }
    }

    /**
     * HALAMAN PRINT (Cetak Laporan Pajak Kritis)
     */
    public function print($lokasi)
    {
        $hariIni = Carbon::today();
        $batasPeringatan = Carbon::today()->addDays(30);

        // Menampilkan seluruh kendaraan kritis untuk dicetak
        $pajaks = Peralatan::where('lokasi', $lokasi)
            ->whereNotNull('nomor_polisi')
            ->whereNotNull('tanggal_pajak')
            ->where(function ($query) use ($hariIni, $batasPeringatan) {
                $query->where('tanggal_pajak', '<=', $hariIni)
                      ->orWhereBetween('tanggal_pajak', [$hariIni, $batasPeringatan]);
            })
            ->orderBy('tanggal_pajak', 'asc')
            ->get();

        return view("pages.{$lokasi}.pajak.print", compact('pajaks', 'lokasi'));
    }
}