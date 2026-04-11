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
        // Mengambil data sampah dari setiap kategori berdasarkan lokasi
        $arsipTanah      = Tanah::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
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
    public function restore($lokasi, $kategori, $kode)
    {
        $model = $this->getModel($kategori);
        $primaryKey = $this->getPrimaryKey($kategori);

        $item = $model::where($primaryKey, $kode)->onlyTrashed()->firstOrFail();
        
        if ($item->lokasi !== $lokasi) abort(403, 'Akses ditolak.');

        $item->restore(); // Memulihkan data

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

        // Jika kategori BMD dan memiliki file BAST, hapus file fisiknya
        if ($kategori == 'bmd' && $item->bast_file) {
            Storage::disk('public')->delete($item->bast_file);
        }

        $item->forceDelete(); // Hapus selamanya

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
        // Rusak menggunakan no_id_pemda
        if ($kategori == 'rusak') {
            return 'no_id_pemda';
        }
        
        // BMD dan Inventaris menggunakan ID (Auto Increment)
        if ($kategori == 'bmd' || $kategori == 'inventaris') {
            return 'id';
        }

        // KIB A, B, C, D menggunakan kode_barang
        return 'kode_barang';
    }
}