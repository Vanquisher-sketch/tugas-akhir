<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\DetailPeralatan; 
use App\Models\Peralatan; 
use App\Models\Ruangan; 
use App\Models\Rusak;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class InventarisController extends Controller
{
    public function index(Request $request, $lokasi, $kode_ruangan)
    {
        $room = Ruangan::findOrFail($kode_ruangan);
        if ($room->lokasi !== $lokasi) { abort(404); }

        $search = $request->query('search');
        $query = Inventaris::where('inv_ruangan_kode', $room->kode_ruangan);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('inv_nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('inv_kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('inv_merk_tipe', 'LIKE', "%{$search}%");
            });
        }

        $dataInventaris = $query->orderBy('inv_kode_barang', 'desc')->paginate(10);
        $allRooms = Ruangan::where('lokasi', $lokasi)->orderBy('ruangan_nama')->get();
        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];

        return view("pages.{$lokasi}.inventaris.index", compact('dataInventaris', 'lokasi', 'room', 'search', 'allRooms', 'daftarSatuan'));
    }

    public function create($lokasi, $kode_ruangan)
    {
        $room = Ruangan::findOrFail($kode_ruangan);
        if ($room->lokasi !== $lokasi) { abort(404); }
        
        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];
        
        $allPeralatan = Peralatan::where('lokasi', $lokasi)->orderBy('alat_nama_barang', 'asc')->get()->map(function ($barang) {
            $terpakai = Inventaris::where('inv_kode_barang', $barang->alat_kode_barang)->sum('inv_jumlah');
            $barang->sisa_stok = (int)$barang->alat_jumlah - (int)$terpakai;
            
            $barang->kode_barang = $barang->alat_kode_barang;
            $barang->nama_barang = $barang->alat_nama_barang;
            $barang->merk_tipe = $barang->alat_merk_tipe ?? '-';
            $barang->tahun_perolehan = $barang->alat_tahun_perolehan ?? '-';
            $barang->satuan = $barang->alat_satuan ?? 'Buah';

            return $barang;
        });

        return view("pages.{$lokasi}.inventaris.create", compact('lokasi', 'room', 'daftarSatuan', 'allPeralatan'));
    }

    public function store(Request $request, $lokasi, $kode_ruangan)
    {
        $room = Ruangan::findOrFail($kode_ruangan);
        if ($room->lokasi !== $lokasi) { abort(404); }

        $request->validate([
            'kode_barang'        => 'required|string|max:100', 
            'jumlah'             => 'required|integer|min:1',
            'satuan'             => 'required|string',
            'kondisi'            => 'required|in:Baik,Rusak Ringan,Rusak Berat', 
        ]);

        $masterPeralatan = Peralatan::where('alat_kode_barang', $request->kode_barang)->first();
        if (!$masterPeralatan) return redirect()->back()->withInput()->with('error', 'Gagal: Kode barang tidak valid di Master KIB B.');

        $totalStokMaster = (int) $masterPeralatan->alat_jumlah;
        $stokTerpakaiDiruangan = (int) Inventaris::where('inv_kode_barang', $request->kode_barang)->sum('inv_jumlah');
        if ((int)$request->jumlah > ($totalStokMaster - $stokTerpakaiDiruangan)) return redirect()->back()->withInput()->with('error', "Gagal! Stok tidak mencukupi.");

        DB::transaction(function () use ($request, $room, $lokasi) {
            
            $exists = Inventaris::where('inv_kode_barang', $request->kode_barang)
                                ->where('inv_ruangan_kode', $room->kode_ruangan)
                                ->where('inv_kondisi', $request->kondisi)
                                ->exists();

            if ($exists) {
                // Gunakan Query Murni agar aman tanpa ID
                Inventaris::where('inv_kode_barang', $request->kode_barang)
                          ->where('inv_ruangan_kode', $room->kode_ruangan)
                          ->where('inv_kondisi', $request->kondisi)
                          ->increment('inv_jumlah', $request->jumlah);
            } else {
                Inventaris::create([
                    'inv_kode_barang'        => $request->kode_barang,
                    'inv_ruangan_kode'       => $room->kode_ruangan,
                    'inv_nibar'              => $request->nibar,
                    'inv_nomor_register'     => $request->nomor_register,
                    'inv_nama_barang'        => $request->nama_barang,
                    'inv_spesifikasi_barang' => $request->spesifikasi_barang,
                    'inv_merk_tipe'          => $request->merk_tipe,
                    'inv_tahun_perolehan'    => ($request->tahun_perolehan === '-' || empty($request->tahun_perolehan)) ? null : $request->tahun_perolehan,
                    'inv_jumlah'             => $request->jumlah,
                    'inv_satuan'             => $request->satuan,
                    'inv_kondisi'            => $request->kondisi,
                    'inv_keterangan'         => $request->keterangan,
                    'lokasi'                 => $lokasi,
                ]);
            }

            $stikerTerakhir = DetailPeralatan::where('dt_alat_kode_barang', $request->kode_barang)->count();
            for ($i = 1; $i <= (int)$request->jumlah; $i++) {
                $nomorUrut = $stikerTerakhir + $i; 
                DetailPeralatan::create([
                    'dt_alat_kode_barang'   => $request->kode_barang, 
                    'dt_alat_kode_barcode'  => $request->kode_barang . '.' . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT), 
                    'dt_alat_kondisi'       => $request->kondisi,
                    'lokasi'                => $room->ruangan_nama, 
                    'dt_alat_status_pinjam' => 'Tersedia', 
                    'dt_alat_tanggal_cek'   => now()->toDateString() 
                ]);
            }
        });

        $this->sendNotification('ditambahkan', $request->nama_barang);
        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan])->with('success', 'Data berhasil disimpan.');
    }

    public function edit(Request $request, $lokasi, $kode_ruangan, $inv_kode_barang)
    {
        $room = Ruangan::findOrFail($kode_ruangan);
        $kondisi = $request->query('kond'); 
        
        $q = Inventaris::where('inv_kode_barang', $inv_kode_barang)->where('inv_ruangan_kode', $kode_ruangan);
        if ($kondisi) { $q->where('inv_kondisi', $kondisi); }
        $inventari = $q->firstOrFail();

        if ($room->lokasi !== $lokasi) { abort(404); }
        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];
        
        return view("pages.{$lokasi}.inventaris.edit", compact('lokasi', 'room', 'inventari', 'daftarSatuan'))->with('item', $inventari);
    }

    public function update(Request $request, $lokasi, $kode_ruangan, $inv_kode_barang)
    {
        $room = Ruangan::findOrFail($kode_ruangan);
        $kondisiLama = $request->query('kond'); 
        
        $q = Inventaris::where('inv_kode_barang', $inv_kode_barang)->where('inv_ruangan_kode', $kode_ruangan);
        if ($kondisiLama) { $q->where('inv_kondisi', $kondisiLama); }
        $inventari = $q->firstOrFail();

        if ($room->lokasi !== $lokasi) { abort(404); }
        
        $request->validate([
            'qty_ubah'     => 'required|integer|min:1|max:' . $inventari->inv_jumlah,
            'kondisi_baru' => 'required|in:Baik,Rusak Ringan,Rusak Berat', 
            'keterangan'   => 'nullable|string'
        ]);

        $qtyUbah = (int) $request->qty_ubah;
        $kondisiBaru = $request->kondisi_baru;
        $kondisiAsli = $inventari->inv_kondisi;

        DB::transaction(function () use ($inventari, $qtyUbah, $kondisiBaru, $kondisiAsli, $request, $room, $lokasi) {
            
            // SKENARIO 1: Jika Diubah Seluruhnya
            if ($qtyUbah === (int)$inventari->inv_jumlah) {
                $targetExists = Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)
                                          ->where('inv_ruangan_kode', $room->kode_ruangan)
                                          ->where('inv_kondisi', $kondisiBaru)
                                          ->where('inv_kondisi', '!=', $kondisiAsli)
                                          ->exists();
                
                if ($targetExists) {
                    // Tambah ke baris yang sudah ada
                    Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)->where('inv_ruangan_kode', $room->kode_ruangan)->where('inv_kondisi', $kondisiBaru)->increment('inv_jumlah', $qtyUbah);
                    // Hapus pakai query murni
                    Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)->where('inv_ruangan_kode', $room->kode_ruangan)->where('inv_kondisi', $kondisiAsli)->delete();
                } else {
                    Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)->where('inv_ruangan_kode', $room->kode_ruangan)->where('inv_kondisi', $kondisiAsli)
                              ->update(['inv_kondisi' => $kondisiBaru, 'inv_keterangan' => $request->keterangan]);
                }
            } 
            // SKENARIO 2: Pecah Baris Sebagian
            else {
                // Kurangi baris saat ini dengan query murni
                Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)->where('inv_ruangan_kode', $room->kode_ruangan)->where('inv_kondisi', $kondisiAsli)->decrement('inv_jumlah', $qtyUbah);

                $targetExists = Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)->where('inv_ruangan_kode', $room->kode_ruangan)->where('inv_kondisi', $kondisiBaru)->exists();
                
                if ($targetExists) {
                    Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)->where('inv_ruangan_kode', $room->kode_ruangan)->where('inv_kondisi', $kondisiBaru)->increment('inv_jumlah', $qtyUbah);
                } else {
                    Inventaris::create([
                        'inv_kode_barang'        => $inventari->inv_kode_barang,
                        'inv_ruangan_kode'       => $inventari->inv_ruangan_kode,
                        'inv_nibar'              => $inventari->inv_nibar,
                        'inv_nomor_register'     => $inventari->inv_nomor_register,
                        'inv_nama_barang'        => $inventari->inv_nama_barang,
                        'inv_spesifikasi_barang' => $inventari->inv_spesifikasi_barang,
                        'inv_merk_tipe'          => $inventari->inv_merk_tipe,
                        'inv_tahun_perolehan'    => $inventari->inv_tahun_perolehan,
                        'inv_jumlah'             => $qtyUbah,
                        'inv_satuan'             => $inventari->inv_satuan,
                        'inv_kondisi'            => $kondisiBaru,
                        'inv_keterangan'         => $request->keterangan,
                        'lokasi'                 => $lokasi,
                    ]);
                }
            }

            // Sync Stiker
            DetailPeralatan::where('dt_alat_kode_barang', $inventari->inv_kode_barang)
                ->where('lokasi', $room->ruangan_nama)
                ->where('dt_alat_kondisi', $kondisiAsli)
                ->take($qtyUbah)
                ->update(['dt_alat_kondisi' => $kondisiBaru]);

            if (in_array($kondisiBaru, ['Rusak Ringan', 'Rusak Berat'])) {
                Rusak::updateOrCreate(
                    ['rusak_kode_barang' => $inventari->inv_kode_barang],
                    ['rusak_jenis_asal' => 'Inventaris', 'rusak_keterangan' => $request->keterangan, 'lokasi' => $lokasi]
                );
            }
        });

        $this->sendNotification('diperbarui kondisinya', $inventari->inv_nama_barang);
        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan])->with('success', 'Kondisi barang berhasil dipecah/diperbarui.');
    }

    public function move(Request $request, $lokasi, $kode_ruangan, $inv_kode_barang)
    {
        $room = Ruangan::findOrFail($kode_ruangan);
        $kondisi = 'Baik'; // Mutasi hanya mengunci pada baris yang Baik
        
        $inventari = Inventaris::where('inv_kode_barang', $inv_kode_barang)
                               ->where('inv_ruangan_kode', $kode_ruangan)
                               ->where('inv_kondisi', $kondisi)
                               ->firstOrFail();

        if ($room->lokasi !== $lokasi) { abort(404); }

        $request->validate([
            'new_room_id' => 'required|exists:ruangans,kode_ruangan',
            'qty_to_move' => 'required|integer|min:1|max:' . $inventari->inv_jumlah,
        ]);

        $qtyPindah = (int) $request->qty_to_move;
        $newRoom = Ruangan::where('kode_ruangan', $request->new_room_id)->firstOrFail();

        try {
            DB::transaction(function () use ($inventari, $newRoom, $qtyPindah, $lokasi, $room, $kondisi) {
                
                $detailUnitDipindah = DetailPeralatan::where('dt_alat_kode_barang', $inventari->inv_kode_barang)
                    ->where('lokasi', $room->ruangan_nama)
                    ->where('dt_alat_kondisi', $kondisi) 
                    ->take($qtyPindah)
                    ->get();

                // Kurangi stok pakai Query Murni
                Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)
                          ->where('inv_ruangan_kode', $room->kode_ruangan)
                          ->where('inv_kondisi', $kondisi)
                          ->decrement('inv_jumlah', $qtyPindah);

                $targetExists = Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)
                                          ->where('inv_ruangan_kode', $newRoom->kode_ruangan)
                                          ->where('inv_kondisi', $kondisi)
                                          ->exists();

                if ($targetExists) {
                    Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)
                              ->where('inv_ruangan_kode', $newRoom->kode_ruangan)
                              ->where('inv_kondisi', $kondisi)
                              ->increment('inv_jumlah', $qtyPindah);
                } else {
                    Inventaris::create([
                        'inv_kode_barang'        => $inventari->inv_kode_barang,
                        'inv_ruangan_kode'       => $newRoom->kode_ruangan,
                        'inv_nibar'              => $inventari->inv_nibar,
                        'inv_nomor_register'     => $inventari->inv_nomor_register,
                        'inv_nama_barang'        => $inventari->inv_nama_barang,
                        'inv_spesifikasi_barang' => $inventari->inv_spesifikasi_barang,
                        'inv_merk_tipe'          => $inventari->inv_merk_tipe,
                        'inv_tahun_perolehan'    => $inventari->inv_tahun_perolehan,
                        'inv_jumlah'             => $qtyPindah,
                        'inv_satuan'             => $inventari->inv_satuan,
                        'inv_kondisi'            => $kondisi,
                        'lokasi'                 => $lokasi,
                    ]);
                }

                foreach ($detailUnitDipindah as $unit) {
                    $unit->update(['lokasi' => $newRoom->ruangan_nama]);
                }

                // Hapus jika sisa stok di ruangan lama = 0 (Pakai Query Murni)
                $sisaStok = Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)
                                      ->where('inv_ruangan_kode', $room->kode_ruangan)
                                      ->where('inv_kondisi', $kondisi)
                                      ->value('inv_jumlah');
                                      
                if ($sisaStok <= 0) {
                    Inventaris::where('inv_kode_barang', $inventari->inv_kode_barang)
                              ->where('inv_ruangan_kode', $room->kode_ruangan)
                              ->where('inv_kondisi', $kondisi)
                              ->delete();
                }
            });

            $this->sendNotification("memindahkan $qtyPindah unit ke {$newRoom->ruangan_nama}", $inventari->inv_nama_barang);
            return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan])->with('success', "Berhasil memindahkan $qtyPindah unit aset.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memindahkan barang: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $lokasi, $kode_ruangan, $inv_kode_barang)
    {
        $room = Ruangan::findOrFail($kode_ruangan);
        $kondisi = $request->query('kond'); 

        if ($room->lokasi !== $lokasi) { abort(404); }
        
        $item = Inventaris::where('inv_kode_barang', $inv_kode_barang)
                          ->where('inv_ruangan_kode', $kode_ruangan);
        if ($kondisi) { $item->where('inv_kondisi', $kondisi); }
        $item = $item->firstOrFail();

        $namaBarang = $item->inv_nama_barang;
        
        // Hapus HANYA baris spesifik tersebut (Query Murni)
        $q = Inventaris::where('inv_kode_barang', $inv_kode_barang)
                       ->where('inv_ruangan_kode', $kode_ruangan);
        if ($kondisi) { $q->where('inv_kondisi', $kondisi); }
        $q->delete();

        $this->sendNotification('dihapus', $namaBarang);
        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan])->with('success', 'Baris data berhasil dihapus.');
    }

    public function showDetail($lokasi, $room_kode, $kode_barang)
    {
        $room = Ruangan::where('kode_ruangan', $room_kode)->firstOrFail();
        $item = Inventaris::where('inv_kode_barang', $kode_barang)->where('inv_ruangan_kode', $room_kode)->firstOrFail();
        $unitPecahan = DetailPeralatan::where('dt_alat_kode_barang', $kode_barang)->get();
        return view("pages.{$lokasi}.inventaris.detail", compact('lokasi', 'room', 'item', 'unitPecahan'));
    }

    public function print($lokasi, $kode_ruangan)
    {
        $room = Ruangan::findOrFail($kode_ruangan);
        if ($room->lokasi !== $lokasi) { abort(404); }
        $dataInventaris = Inventaris::where('inv_ruangan_kode', $room->kode_ruangan)->orderBy('inv_nama_barang', 'asc')->get();
        return view("pages.{$lokasi}.inventaris.print", compact('dataInventaris', 'lokasi', 'room'));
    }

    private function sendNotification($action, $namaBarang)
    {
        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), $action, 'Inventaris', $namaBarang));
        }
    }
}