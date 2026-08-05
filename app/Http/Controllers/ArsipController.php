<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// Import semua model aset
use App\Models\Tanah;
use App\Models\Peralatan;
use App\Models\Gedung;
use App\Models\Jalan;
use App\Models\Rusak;
use App\Models\Bmd;
use App\Models\Inventaris;

class ArsipController extends Controller
{
    /**
     * Menampilkan semua data yang dihapus dari seluruh kategori (Sistem Tab)
     */
    public function index($lokasi)
    {
        $arsipTanah       = Tanah::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipPeralatan   = Peralatan::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipGedung      = Gedung::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipJalan       = Jalan::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipRusak       = Rusak::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipBmd         = Bmd::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipInventaris  = Inventaris::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();

        return view('pages.arsip.index', compact(
            'lokasi', 
            'arsipTanah', 
            'arsipPeralatan', 
            'arsipGedung', 
            'arsipJalan', 
            'arsipRusak', 
            'arsipBmd', 
            'arsipInventaris'
        ));
    }

    /**
     * Mengembalikan data (Restore) ke daftar aktif
     */
    public function restore($lokasi, $kategori, $kode) // <-- FIX: Menggunakan variabel generik $kode
    {
        $model = $this->getModel($kategori);
        $primaryKey = $this->getPrimaryKey($kategori);

        // Cari data menggunakan primary key yang sudah disesuaikan
        $item = $model::where($primaryKey, $kode)->onlyTrashed()->firstOrFail();
        
        if ($item->lokasi !== $lokasi) abort(403, 'Akses ditolak.');

        $item->restore();

        return redirect()->back()->with('success', 'Data berhasil dipulihkan dari arsip.');
    }

    /**
     * Menghapus data secara permanen dari database
     */
    public function forceDelete($lokasi, $kategori, $kode)
    {
        $model = $this->getModel($kategori);
        $primaryKey = $this->getPrimaryKey($kategori);

        $item = $model::where($primaryKey, $kode)->onlyTrashed()->firstOrFail();
        
        if ($item->lokasi !== $lokasi) abort(403, 'Akses ditolak.');

        // Hapus file fisik jika kategori BMD
        if ($kategori == 'bmd' && $item->bast_file) {
            Storage::disk('public')->delete($item->bast_file);
        }

        $item->forceDelete();

        return redirect()->back()->with('success', 'Data telah dihapus secara permanen.');
    }

    /**
     * Helper: Menentukan Model berdasarkan kategori
     */
    private function getModel($kategori) {
        $map = [
            'tanah'      => Tanah::class,
            'peralatan'  => Peralatan::class,
            'gedung'     => Gedung::class,
            'jalan'      => Jalan::class,
            'rusak'      => Rusak::class,
            'bmd'        => Bmd::class,
            'inventaris' => Inventaris::class,
        ];

        return $map[$kategori] ?? abort(404);
    }

    /**
     * Helper: Menentukan Primary Key (Kunci Utama) unik tiap kategori
     */
    private function getPrimaryKey($kategori) {
        // FIX: Mapping spesifik untuk menyesuaikan struktur database PANDAWA
        $keyMap = [
            'tanah'      => 'tanah_kode_barang',
            'peralatan'  => 'alat_kode_barang',
            'gedung'     => 'gedung_kode_barang',
            'jalan'      => 'jalan_kode_barang',
            'inventaris' => 'inv_kode_barang',
            'rusak'      => 'rusak_kode_barang', 
            'bmd'        => 'id',
        ];

        return $keyMap[$kategori] ?? abort(404, 'Kategori tidak dikenali.');
    }
}