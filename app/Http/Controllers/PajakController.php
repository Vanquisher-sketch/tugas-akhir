<?php

namespace App\Http\Controllers;

use App\Models\Bmd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PajakController extends Controller
{
    /**
     * MENAMPILKAN DATA (INDEX)
     * Menampilkan daftar aset dari tabel BMD untuk dimonitor pajaknya.
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');

        // Optimasi Query dengan 'when'
        $pajaks = Bmd::with('peralatan')
            ->where('lokasi', $lokasi)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pemakai_nama', 'LIKE', "%{$search}%")
                      ->orWhere('nomor_pemakai', 'LIKE', "%{$search}%")   // Cari via No HP Pemakai
                      ->orWhere('nomor_bendahara', 'LIKE', "%{$search}%") // Cari via No HP Bendahara
                      ->orWhereHas('peralatan', function ($subQ) use ($search) {
                          $subQ->where('nama_barang', 'LIKE', "%{$search}%")
                               ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                               ->orWhere('nibr', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->latest() // Mengurutkan berdasarkan update terakhir
            ->paginate(10)
            ->withQueryString(); 
        
        return view("pages.{$lokasi}.pajak.index", compact('pajaks', 'lokasi', 'search'));
    }

    /**
     * FORM EDIT (Hanya untuk Update Kontak & Tanggal)
     */
    public function edit($lokasi, $id)
    {
        // Cari data BMD berdasarkan ID dan Lokasi (Security check)
        $pajak = Bmd::with('peralatan')
                    ->where('lokasi', $lokasi)
                    ->findOrFail($id);

        return view("pages.{$lokasi}.pajak.edit", compact('pajak', 'lokasi'));
    }

    /**
     * PROSES UPDATE
     * Mengupdate nomor HP dan DUA JENIS TANGGAL (Pajak Tahunan & STNK 5 Tahunan)
     */
    public function update(Request $request, $lokasi, $id)
    {
        // 1. Validasi Input
        // [REVISI] Menambahkan validasi untuk 'tanggal_stnk'
        $validated = $request->validate([
            'nomor_pemakai'   => 'nullable|numeric|digits_between:10,15',
            'nomor_bendahara' => 'nullable|numeric|digits_between:10,15',
            'tanggal_pajak'   => 'nullable|date', // Pajak Tahunan
            'tanggal_stnk'    => 'nullable|date', // Pajak 5 Tahunan (Ganti Kaleng)
        ]);

        // 2. Security Check & Retrieve Data
        $bmd = Bmd::where('lokasi', $lokasi)->findOrFail($id);

        // 3. Proses Update dengan Transaction & Try-Catch
        DB::beginTransaction();

        try {
            $bmd->update([
                'nomor_pemakai'   => $validated['nomor_pemakai'],
                'nomor_bendahara' => $validated['nomor_bendahara'],
                'tanggal_pajak'   => $validated['tanggal_pajak'], // Simpan Tgl Pajak Tahunan
                'tanggal_stnk'    => $validated['tanggal_stnk'],  // [BARU] Simpan Tgl Ganti Kaleng
            ]);

            DB::commit();

            return redirect()->route('lokasi.pajak.index', $lokasi)
                             ->with('success', 'Data Kontak, Pajak Tahunan, dan STNK berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Update Pajak BMD: " . $e->getMessage());
            
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal memperbarui data pajak. Silakan coba lagi.');
        }
    }

    /**
     * HALAMAN PRINT (Cetak Laporan)
     * Menampilkan semua data tanpa pagination untuk dicetak browser/PDF.
     */
    public function print($lokasi)
    {
        // Ambil SEMUA data (get) tanpa pagination
        $pajaks = Bmd::with('peralatan')
                    ->where('lokasi', $lokasi)
                    ->latest() // Urutkan dari yang terbaru atau bisa ->orderBy('tanggal_pajak', 'asc')
                    ->get();

        return view("pages.{$lokasi}.pajak.print", compact('pajaks', 'lokasi'));
    }
}