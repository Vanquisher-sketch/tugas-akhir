<?php

namespace App\Http\Controllers;

use App\Models\DetailPeralatan;
use App\Models\Inventaris;
use App\Models\Peralatan;
use App\Models\Rusak;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class RusakController extends Controller
{
    /**
     * Tampilkan Jurnal Kerusakan Lintas Modul
     */
    public function index(Request $request, string $lokasi)
    {
        $search = $request->query('search');

        // 1. Ambil data dasar jurnal rusak
        $jurnalRusak = Rusak::where('lokasi', $lokasi)->get();

        // Optimasi: Pre-fetch data master untuk menghindari N+1 query
        $kodePeralatan = $jurnalRusak->where('rusak_jenis_asal', 'Peralatan')->pluck('rusak_kode_barang')->unique();
        $kodeInventaris = $jurnalRusak->where('rusak_jenis_asal', 'Inventaris')->pluck('rusak_kode_barang')->unique();

        $mapPeralatan = Peralatan::where('lokasi', $lokasi)
            ->whereIn('alat_kode_barang', $kodePeralatan)
            ->get()
            ->keyBy('alat_kode_barang');

        $mapInventaris = Inventaris::where('lokasi', $lokasi)
            ->whereIn('inv_kode_barang', $kodeInventaris)
            ->get()
            ->keyBy('inv_kode_barang');

        // 2. Mapping data secara live
        $mappedData = $jurnalRusak->map(function ($item) use ($mapPeralatan, $mapInventaris) {
            if ($item->rusak_jenis_asal === 'Peralatan') {
                $detail = $mapPeralatan->get($item->rusak_kode_barang);

                $item->nama_barang     = $detail->alat_nama_barang ?? 'Aset Telah Diarsip';
                $item->spesifikasi     = $detail->alat_merk_tipe ?? '-';
                $item->no_polisi       = $detail->alat_nomor_polisi ?? '-';
                $item->tahun_perolehan = $detail->alat_tanggal_perolehan ?? '-';
                $item->harga_perolehan = $detail->alat_nilai_perolehan ?? 0;
            } elseif ($item->rusak_jenis_asal === 'Inventaris') {
                $detail = $mapInventaris->get($item->rusak_kode_barang);

                $item->nama_barang     = $detail->inv_nama_barang ?? 'Aset Telah Diarsip';
                $item->spesifikasi     = $detail->inv_merk_tipe ?? 'Inventaris Ruangan';
                $item->no_polisi       = '-';
                $item->tahun_perolehan = $detail->inv_tahun_perolehan ?? '-';
                $item->harga_perolehan = 0;
            }

            return $item;
        });

        // 3. Filter fitur pencarian
        if ($search) {
            $mappedData = $mappedData->filter(function ($item) use ($search) {
                return false !== stripos($item->nama_barang, $search) ||
                       false !== stripos($item->rusak_kode_barang, $search) ||
                       false !== stripos($item->rusak_keterangan, $search);
            });
        }

        $cleanCollection = $mappedData->values();

        // 4. Paginator manual
        $perPage = 10;
        $page = $request->query('page', 1);

        $dataRusak = new LengthAwarePaginator(
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
    public function destroy(Request $request, string $lokasi, string $id)
    {
        // Cari record rusak murni menggunakan rusak_kode_barang
        $rusak = Rusak::where('lokasi', $lokasi)
            ->where('rusak_kode_barang', $id)
            ->firstOrFail();

        $itemName = 'Aset';

        DB::transaction(function () use ($rusak, $lokasi, &$itemName) {
            $kodeBarang = $rusak->rusak_kode_barang;

            if ($rusak->rusak_jenis_asal === 'Peralatan') {
                $detail = Peralatan::where('lokasi', $lokasi)
                    ->where('alat_kode_barang', $kodeBarang)
                    ->first();

                $itemName = $detail->alat_nama_barang ?? 'Aset Peralatan';

                Peralatan::where('lokasi', $lokasi)
                    ->where('alat_kode_barang', $kodeBarang)
                    ->update([
                        'alat_kondisi'    => 'Baik',
                        'alat_keterangan' => null,
                    ]);

            } elseif ($rusak->rusak_jenis_asal === 'Inventaris') {
                $detail = Inventaris::where('lokasi', $lokasi)
                    ->where('inv_kode_barang', $kodeBarang)
                    ->first();

                $itemName = $detail->inv_nama_barang ?? 'Aset Inventaris';

                // Ambil semua record inventaris berstatus rusak untuk kode barang ini
                $inventarisRusakList = Inventaris::where('lokasi', $lokasi)
                    ->where('inv_kode_barang', $kodeBarang)
                    ->whereIn('inv_kondisi', ['Rusak Ringan', 'Rusak Berat'])
                    ->get();

                foreach ($inventarisRusakList as $invRusak) {
                    $qtyRusak = $invRusak->inv_jumlah;
                    $ruanganKode = $invRusak->inv_ruangan_kode;

                    // Periksa apakah sudah ada baris kondisi 'Baik' di ruangan yang sama
                    $targetBaik = Inventaris::withTrashed()
                        ->where('lokasi', $lokasi)
                        ->where('inv_kode_barang', $kodeBarang)
                        ->where('inv_ruangan_kode', $ruanganKode)
                        ->where('inv_kondisi', 'Baik')
                        ->first();

                    if ($targetBaik) {
                        if ($targetBaik->trashed()) {
                            $targetBaik->restore();
                            $targetBaik->inv_jumlah = 0;
                        }

                        // Gabungkan stok ke baris kondisi Baik yang ada
                        $targetBaik->increment('inv_jumlah', $qtyRusak);
                        $targetBaik->update(['inv_keterangan' => null]);

                        // Nolkan stok dan soft delete baris rusak agar tidak memicu bentrok primary key
                        $invRusak->update(['inv_jumlah' => 0]);
                        $invRusak->delete();
                    } else {
                        // Jika belum ada baris kondisi 'Baik', ubah kondisinya secara langsung
                        $invRusak->update([
                            'inv_kondisi'    => 'Baik',
                            'inv_keterangan' => null,
                        ]);
                    }
                }
            }

            // Sinkronkan kondisi unit pada detail barcode/stiker
            DetailPeralatan::where('dt_alat_kode_barang', $kodeBarang)
                ->whereIn('dt_alat_kondisi', ['Rusak Ringan', 'Rusak Berat'])
                ->update(['dt_alat_kondisi' => 'Baik']);

            // Hapus record dari jurnal rusak
            $rusak->delete();
        });

        // Kirim log notifikasi
        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if (class_exists(DataModificationNotification::class) && $recipients->isNotEmpty()) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), 'dihapus', 'Barang Rusak', $itemName));
        }

        return redirect()->route('lokasi.rusak.index', ['lokasi' => $lokasi])
            ->with('success', "Barang {$itemName} selesai diperbaiki. Status kondisi di modul asal kembali dipulihkan ke status Baik.");
    }

    /**
     * Cetak Laporan PDF Jurnal Kerusakan
     */
    public function print(string $lokasi)
    {
        $jurnalRusak = Rusak::where('lokasi', $lokasi)->get();

        $kodePeralatan = $jurnalRusak->where('rusak_jenis_asal', 'Peralatan')->pluck('rusak_kode_barang')->unique();
        $kodeInventaris = $jurnalRusak->where('rusak_jenis_asal', 'Inventaris')->pluck('rusak_kode_barang')->unique();

        $mapPeralatan = Peralatan::where('lokasi', $lokasi)
            ->whereIn('alat_kode_barang', $kodePeralatan)
            ->get()
            ->keyBy('alat_kode_barang');

        $mapInventaris = Inventaris::where('lokasi', $lokasi)
            ->whereIn('inv_kode_barang', $kodeInventaris)
            ->get()
            ->keyBy('inv_kode_barang');

        $dataRusak = $jurnalRusak->map(function ($item) use ($mapPeralatan, $mapInventaris) {
            if ($item->rusak_jenis_asal === 'Peralatan') {
                $detail = $mapPeralatan->get($item->rusak_kode_barang);

                $item->nama_barang     = $detail->alat_nama_barang ?? 'Aset Diarsip';
                $item->spesifikasi     = $detail->alat_merk_tipe ?? '-';
                $item->no_polisi       = $detail->alat_nomor_polisi ?? '-';
                $item->tahun_perolehan = $detail->alat_tanggal_perolehan ?? '-';
                $item->harga_perolehan = $detail->alat_nilai_perolehan ?? 0;
            } else {
                $detail = $mapInventaris->get($item->rusak_kode_barang);

                $item->nama_barang     = $detail->inv_nama_barang ?? 'Aset Diarsip';
                $item->spesifikasi     = $detail->inv_merk_tipe ?? 'Inventaris Ruangan';
                $item->no_polisi       = '-';
                $item->tahun_perolehan = $detail->inv_tahun_perolehan ?? '-';
                $item->harga_perolehan = 0;
            }
            return $item;
        });

        return view("pages.{$lokasi}.rusak.print", compact('dataRusak', 'lokasi'));
    }
}