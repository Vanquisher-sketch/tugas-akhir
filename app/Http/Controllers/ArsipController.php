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
        $arsipTanah      = Tanah::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipPeralatan  = Peralatan::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipGedung     = Gedung::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipJalan      = Jalan::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipRusak      = Rusak::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipBmd        = Bmd::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();
        $arsipInventaris = Inventaris::where('lokasi', $lokasi)->onlyTrashed()->latest('deleted_at')->get();

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
        $item = $this->findArsipItem($lokasi, $kategori, $kode);

        // PENANGANAN KHUSUS KATEGORI INVENTARIS
        if ($kategori === 'inventaris') {
            // Pastikan Master KIB B ter-restore jika sebelumnya terhapus (soft delete)
            Peralatan::onlyTrashed()
                ->where('lokasi', $lokasi)
                ->where('alat_kode_barang', $item->inv_kode_barang)
                ->restore();

            $existingActive = Inventaris::where('lokasi', $lokasi)
                ->where('inv_ruangan_kode', $item->inv_ruangan_kode) 
                ->where('inv_kode_barang', $item->inv_kode_barang)
                ->where('inv_kondisi', $item->inv_kondisi)
                ->first();

            $restoredQty = ($item->inv_jumlah > 0) ? $item->inv_jumlah : 1;

            if ($existingActive) {
                // Jika data aktif sudah ada di ruangan & kondisi sama, gabungkan jumlahnya
                $existingActive->inv_jumlah += $restoredQty;
                $existingActive->save();

                // Hapus data arsip karena jumlahnya sudah dilebur
                $item->forceDelete();
            } else {
                if ($item->inv_jumlah <= 0) {
                    $item->inv_jumlah = 1;
                }
                $item->restore();
            }
        } elseif ($kategori === 'peralatan') {
            // Restore Master Peralatan (KIB B)
            $item->restore();
        } else {
            // Kategori selain inventaris & peralatan
            $item->restore();
        }

        return redirect()->back()->with('success', 'Data berhasil dipulihkan dari arsip.');
    }

    /**
     * Menghapus data secara permanen dari database
     */
    public function forceDelete($lokasi, $kategori, $kode)
    {
        $item = $this->findArsipItem($lokasi, $kategori, $kode);

        if ($kategori === 'inventaris') {
            $kodeBarang = $item->inv_kode_barang;

            // 1. Hapus permanen item Inventaris ini
            $item->forceDelete();

            // 2. Cek apakah masih ada data Inventaris lain yang mengacu pada kode barang ini
            $masihAdaInventaris = Inventaris::withTrashed()
                ->where('lokasi', $lokasi)
                ->where('inv_kode_barang', $kodeBarang)
                ->exists();

            // Jika sudah tidak ada alokasi inventaris tersisa, hapus permanen juga master Peralatan (KIB B)
            if (!$masihAdaInventaris) {
                Peralatan::withTrashed()
                    ->where('lokasi', $lokasi)
                    ->where('alat_kode_barang', $kodeBarang)
                    ->forceDelete();
            }

        } elseif ($kategori === 'peralatan') {
            $kodeBarang = $item->alat_kode_barang;

            // 1. Hapus permanen seluruh alokasi Inventaris yang terhubung dengan master KIB B ini
            Inventaris::withTrashed()
                ->where('lokasi', $lokasi)
                ->where('inv_kode_barang', $kodeBarang)
                ->forceDelete();

            // 2. Hapus permanen master Peralatan (KIB B)
            $item->forceDelete();

        } else {
            // Kategori lainnya
            if ($kategori === 'bmd' && $item->bast_file) {
                Storage::disk('public')->delete($item->bast_file);
            }
            $item->forceDelete();
        }

        return redirect()->back()->with('success', 'Data telah dihapus secara permanen beserta data terhubungnya.');
    }

    /**
     * Helper: Pencarian data arsip terhapus (onlyTrashed) berdasarkan kategori dan kode
     */
    private function findArsipItem($lokasi, $kategori, $kode)
    {
        $model = $this->getModel($kategori);
        $query = $model::onlyTrashed()->where('lokasi', $lokasi);

        if ($kategori === 'inventaris') {
            // Memecah composite key (format: kode_barang|kode_ruangan|inv_kondisi)
            $parts = explode('|', $kode);

            if (count($parts) === 3) {
                return $query->where('inv_kode_barang', $parts[0])
                             ->where('inv_ruangan_kode', $parts[1]) 
                             ->where('inv_kondisi', $parts[2])
                             ->firstOrFail();
            }

            return $query->where('inv_kode_barang', $kode)->firstOrFail();
        }

        $primaryKey = $this->getPrimaryKey($kategori);
        return $query->where($primaryKey, $kode)->firstOrFail();
    }

    /**
     * Helper: Menentukan Model berdasarkan kategori
     */
    private function getModel($kategori)
    {
        $map = [
            'tanah'      => Tanah::class,
            'peralatan'  => Peralatan::class,
            'gedung'     => Gedung::class,
            'jalan'      => Jalan::class,
            'rusak'      => Rusak::class,
            'bmd'        => Bmd::class,
            'inventaris' => Inventaris::class,
        ];

        return $map[$kategori] ?? abort(404, 'Kategori tidak valid.');
    }

    /**
     * Helper: Menentukan Primary Key unik tiap kategori
     */
    private function getPrimaryKey($kategori)
    {
        $keyMap = [
            'tanah'     => 'tanah_kode_barang',
            'peralatan' => 'alat_kode_barang',
            'gedung'    => 'gedung_kode_barang',
            'jalan'     => 'jalan_kode_barang',
            'rusak'     => 'rusak_kode_barang',
            'bmd'       => 'id',
        ];

        return $keyMap[$kategori] ?? abort(404, 'Kategori tidak dikenali.');
    }
}