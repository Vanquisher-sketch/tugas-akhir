<?php

namespace App\Http\Controllers;

use App\Models\Rusak;
use App\Models\Peralatan;
use App\Models\Inventaris;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class RusakController extends Controller
{
    /**
     * Tampilkan Jurnal Kerusakan Lintas Modul (VERSI FIX PAGINATION)
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        
        // 1. Ambil data dasar dari jurnal rusak berdasarkan lokasi wilayah
        $jurnalRusak = Rusak::where('lokasi', $lokasi)->get();

        // 2. Mapping data secara live menggunakan paduan kolom 'kode_barang'
        $mappedData = $jurnalRusak->map(function ($item) use ($lokasi) {
            $detail = null;

            if ($item->jenis_asal === 'Peralatan') {
                $detail = Peralatan::where('lokasi', $lokasi)->where('kode_barang', $item->kode_barang)->first();
                $item->nama_barang = $detail->nama_barang ?? 'Aset Telah Diarsip';
                $item->spesifikasi = $detail->merk_tipe ?? '-';
                $item->no_polisi = $detail->nomor_polisi ?? '-';
                $item->tahun_perolehan = $detail->tahun_perolehan ?? '-';
                $item->harga_perolehan = $detail->harga_perolehan ?? 0;
            } elseif ($item->jenis_asal === 'Inventaris') {
                $detail = Inventaris::where('kode_barang', $item->kode_barang)->first();
                $item->nama_barang = $detail->nama_barang ?? 'Aset Telah Diarsip';
                $item->spesifikasi = 'Inventaris Ruangan';
                $item->no_polisi = '-';
                $item->tahun_perolehan = $detail->tahun_perolehan ?? '-';
                $item->harga_perolehan = $detail->harga_perolehan ?? 0;
            }

            return $item;
        });

        // 3. Filter fitur pencarian jika admin mengetikkan keyword
        if ($search) {
            $mappedData = $mappedData->filter(function ($item) use ($search) {
                return false !== stripos($item->nama_barang, $search) || 
                       false !== stripos($item->kode_barang, $search) ||
                       false !== stripos($item->keterangan, $search);
            });
        }

        // 🌟 FIX EROR: Reset index collection setelah di-filter agar berurutan kembali (0, 1, 2...)
        $cleanCollection = $mappedData->values();

        // 4. Integrasikan ke dalam LengthAwarePaginator manual
        $perPage = 10;
        $page = $request->query('page', 1);
        
        $dataRusak = new \Illuminate\Pagination\LengthAwarePaginator(
            $cleanCollection->forPage($page, $perPage),
            $cleanCollection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view("pages.{$lokasi}.rusak.index", compact('dataRusak', 'lokasi', 'search'));
    }

    /**
     * Selesai Perbaikan (Destroy)
     * Mengeluarkan aset dari daftar rusak & memulihkan kondisi fisik asal menjadi Baik
     */
    public function destroy($lokasi, $id)
    {
        $rusak = Rusak::where('lokasi', $lokasi)->findOrFail($id);
        
        // Ambil nama barang dari mapping dinamis sebelum data jurnalnya dihapus
        $itemName = 'Aset';
        if ($rusak->jenis_asal === 'Peralatan') {
            $detail = Peralatan::where('lokasi', $lokasi)->where('kode_barang', $rusak->kode_barang)->first();
            $itemName = $detail->nama_barang ?? 'Aset Peralatan';
            
            // Kembalikan status ke 'Baik' dan kosongkan deskripsi keterangan kerusakan
            Peralatan::where('lokasi', $lokasi)->where('kode_barang', $rusak->kode_barang)->update(['kondisi' => 'Baik', 'keterangan' => null]);
        } elseif ($rusak->jenis_asal === 'Inventaris') {
            $detail = Inventaris::where('kode_barang', $rusak->kode_barang)->first();
            $itemName = $detail->nama_barang ?? 'Aset Inventaris';
            
            Inventaris::where('kode_barang', $rusak->kode_barang)->update(['kondisi' => 'Baik', 'keterangan' => null]);
        }

        $rusak->delete();

        // Kirim log notifikasi modifikasi data ke admin pusat/super admin
        $recipients = User::whereIn('role_id', [1, 2])->get();
        if (class_exists(\App\Notifications\DataModificationNotification::class)) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), 'dihapus', 'Barang Rusak', $itemName));
        }

        return redirect()->route('lokasi.rusak.index', ['lokasi' => $lokasi])
                         ->with('success', "Barang {$itemName} selesai diperbaiki. Status kondisi di modul asal kembali dipulihkan ke status Baik.");
    }

    /**
     * Cetak Laporan PDF Jurnal Kerusakan
     */
    public function print($lokasi)
    {
        $jurnalRusak = Rusak::where('lokasi', $lokasi)->get();
        
        $dataRusak = $jurnalRusak->map(function ($item) use ($lokasi) {
            if ($item->jenis_asal === 'Peralatan') {
                $detail = Peralatan::where('lokasi', $lokasi)->where('kode_barang', $item->kode_barang)->first();
                $item->nama_barang = $detail->nama_barang ?? 'Aset Diarsip';
                $item->spesifikasi = $detail->merk_tipe ?? '-';
                $item->no_polisi = $detail->nomor_polisi ?? '-';
                $item->tahun_perolehan = $detail->tahun_perolehan ?? '-';
                $item->harga_perolehan = $detail->harga_perolehan ?? 0;
            } else {
                $detail = Inventaris::where('kode_barang', $item->kode_barang)->first();
                $item->nama_barang = $detail->nama_barang ?? 'Aset Diarsip';
                $item->spesifikasi = 'Inventaris Ruangan';
                $item->no_polisi = '-';
                $item->tahun_perolehan = $detail->tahun_perolehan ?? '-';
                $item->harga_perolehan = $detail->harga_perolehan ?? 0;
            }
            return $item;
        });

        return view("pages.{$lokasi}.rusak.print", compact('dataRusak', 'lokasi'));
    }
}