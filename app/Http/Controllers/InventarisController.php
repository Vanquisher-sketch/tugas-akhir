<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\DetailInventaris; 
use App\Models\Peralatan; // Master KIB B
use App\Models\Room;
use App\Models\Rusak;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class InventarisController extends Controller
{
    public function index(Request $request, $lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) { abort(404); }

        $search = $request->query('search');
        $query = Inventaris::where('room_kode', $room->kode_ruangan);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                    ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                    ->orWhere('merk_tipe', 'LIKE', "%{$search}%");
            });
        }

        $dataInventaris = $query->orderBy('kode_barang', 'desc')->paginate(10);
        $allRooms = Room::where('lokasi', $lokasi)->orderBy('name')->get();
        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];

        return view("pages.{$lokasi}.inventaris.index", compact('dataInventaris', 'lokasi', 'room', 'search', 'allRooms', 'daftarSatuan'));
    }

    public function create($lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) { abort(404); }
        
        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];
        
        // 🌟 REVISI SMART DROPDOWN: Hitung sisa stok sebelum dikirim ke view form tambah
        $masterPeralatan = Peralatan::orderBy('nama_barang', 'asc')->get()->map(function ($barang) {
            // Hitung total unit barang ini yang sudah ditaruh di berbagai ruangan
            $terpakai = Inventaris::where('kode_barang', $barang->kode_barang)->sum('jumlah');
            
            // Tanam properti sisa_stok secara dinamis
            $barang->sisa_stok = (int)$barang->jumlah - (int)$terpakai;
            return $barang;
        });

        return view("pages.{$lokasi}.inventaris.create", compact('lokasi', 'room', 'daftarSatuan', 'masterPeralatan'));
    }

    public function store(Request $request, $lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) { abort(404); }

        $validated = $request->validate([
            'kode_barang'        => 'required|string|max:100', 
            'nibar'              => 'nullable|string|max:255',
            'nomor_register'     => 'nullable|string|max:255',
            'jumlah'             => 'required|integer|min:1',
            'satuan'             => 'required|string',
            'kondisi'            => 'required|in:Baik,Rusak Ringan,Rusak Berat', 
            'keterangan'         => 'nullable|string',
            'nama_barang'        => 'nullable|string|max:255',
            'merk_tipe'          => 'nullable|string|max:255',
            'tahun_perolehan'    => 'nullable',
            'spesifikasi_barang' => 'nullable|string',
        ]);

        $validated['room_kode'] = $room->kode_ruangan;
        $validated['lokasi']    = $lokasi; 

        // 🌟 PROTES VALIDASI SISA STOK BACKEND (ANTI JEBOL)
        $masterPeralatan = Peralatan::where('kode_barang', $request->kode_barang)->first();
        if (!$masterPeralatan) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Kode barang tidak valid di Master KIB B.');
        }

        $totalStokMaster = (int) $masterPeralatan->jumlah;
        $stokTerpakaiDiruangan = (int) Inventaris::where('kode_barang', $request->kode_barang)->sum('jumlah');
        $sisaStokTersedia = $totalStokMaster - $stokTerpakaiDiruangan;

        if ((int)$request->jumlah > $sisaStokTersedia) {
            return redirect()->back()->withInput()->with('error', "Gagal disimpan! Kuantitas tidak mencukupi. Total di Master KIB B ada {$totalStokMaster} unit, sudah terbagi di ruangan lain sebanyak {$stokTerpakaiDiruangan} unit. Sisa jatah maksimal penempatan: {$sisaStokTersedia} unit.");
        }

        // DATABASE TRANSACTION: Proses eksekusi double input (Induk & Barcode Manifes Pecahan)
        $inventaris = DB::transaction(function () use ($validated, $room, $lokasi) {
            $item = Inventaris::create($validated);

            $jumlahUnit = (int) $item->jumlah;
            for ($i = 1; $i <= $jumlahUnit; $i++) {
                $noUrutBuntut = str_pad($i, 4, '0', STR_PAD_LEFT);
                
                DetailInventaris::create([
                    'id_barang'     => $item->kode_barang, 
                    'kode_barcode'  => $item->kode_barang . '.' . $noUrutBuntut, 
                    'kondisi'       => $item->kondisi,
                    'lokasi'        => $room->name, 
                    'status_pinjam' => 'Tersedia', 
                    'tanggal_cek'   => now()->toDateString() 
                ]);
            }

            if ($item->kondisi === 'Rusak Berat') {
                Rusak::updateOrCreate(
                    ['kode_barang' => $item->kode_barang],
                    [
                        'jenis_asal'  => 'Inventaris',
                        'keterangan'  => $item->keterangan ?? "Aset Inventaris Ruangan {$room->name} mengalami kerusakan berat.",
                        'lokasi'      => $lokasi
                    ]
                );
            }

            return $item;
        });

        $namaBarangMaster = optional($inventaris->peralatan)->nama_barang ?? 'Aset Baru';
        $this->sendNotification('ditambahkan', $namaBarangMaster);

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])
            ->with('success', 'Data induk inventaris dan baris pecahan detail berhasil disimpan.');
    }

    public function edit($lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }

        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];
        return view("pages.{$lokasi}.inventaris.edit", compact('lokasi', 'room', 'inventari', 'daftarSatuan'));
    }

    public function update(Request $request, $lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }
        
        $validated = $request->validate([
            'nibar'           => 'nullable', 
            'nomor_register'  => 'nullable', 
            'kode_barang'     => 'required|string',
            'nama_barang'     => 'required', 
            'tahun_perolehan' => 'required|digits:4',
            'jumlah'          => 'required|integer|min:0', 
            'satuan'          => 'required',
            'kondisi'         => 'required|in:Baik,Rusak Ringan,Rusak Berat', 
            'keterangan'      => 'nullable|string'
        ]);

        $inventari->update($validated);

        if ($inventari->kondisi === 'Rusak Berat') {
            Rusak::updateOrCreate(
                ['kode_barang' => $inventari->kode_barang],
                [
                    'jenis_asal'  => 'Inventaris',
                    'keterangan'  => $inventari->keterangan ?? "Aset Inventaris Ruangan {$room->name} mengalami kerusakan berat.",
                    'lokasi'      => $lokasi
                ]
            );
        } else {
            Rusak::where('kode_barang', $inventari->kode_barang)->delete();
        }

        $this->sendNotification('diperbarui', $inventari->nama_barang);

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])->with('success', 'Data inventaris diperbarui.');
    }

    public function move(Request $request, $lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }

        $request->validate([
            'new_room_id' => 'required|exists:rooms,kode_ruangan',
            'qty_to_move' => 'required|integer|min:1',
        ]);

        $qtyPindah = (int) $request->qty_to_move;
        $newRoom = Room::where('kode_ruangan', $request->new_room_id)->firstOrFail();

        if ($qtyPindah > $inventari->jumlah) {
            return redirect()->back()->with('error', "Gagal: Stok tidak mencukupi.");
        }

        try {
            DB::transaction(function () use ($inventari, $newRoom, $qtyPindah, $lokasi, $room) {
                $detailUnitDipindah = DetailInventaris::where('id_barang', $inventari->kode_barang)
                    ->where('lokasi', $room->name)
                    ->take($qtyPindah)
                    ->get();

                $inventari->decrement('jumlah', $qtyPindah);

                $targetItem = Inventaris::where('kode_barang', $inventari->kode_barang)
                    ->where('room_kode', $newRoom->kode_ruangan)
                    ->first();

                if ($targetItem) {
                    $targetItem->update([
                        'jumlah' => $targetItem->jumlah + $qtyPindah
                    ]);
                } else {
                    $newItem = $inventari->replicate();
                    $newItem->kode_barang = $inventari->kode_barang; 
                    $newItem->room_kode = $newRoom->kode_ruangan;
                    $newItem->jumlah = $qtyPindah;
                    $newItem->lokasi = $lokasi;
                    $newItem->save();
                }

                foreach ($detailUnitDipindah as $unit) {
                    $unit->update([
                        'lokasi' => $newRoom->name
                    ]);
                }

                if ($inventari->fresh()->jumlah <= 0) {
                    Rusak::where('kode_barang', $inventari->kode_barang)->delete();
                    $inventari->delete();
                }
            });

            $this->sendNotification("memindahkan $qtyPindah unit ke {$newRoom->name}", $inventari->nama_barang);
            return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])
                ->with('success', "Berhasil memindahkan $qtyPindah unit aset beserta data manifes stiker detailnya.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memindahkan barang: ' . $e->getMessage());
        }
    }

    public function destroy($lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }
        $namaBarang = $inventari->nama_barang;
        
        Rusak::where('kode_barang', $inventari->kode_barang)->delete();
        $inventari->delete(); 

        $this->sendNotification('dihapus permanen', $namaBarang);
        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])->with('success', 'Data berhasil dihapus.');
    }

    public function showDetail($lokasi, $room_kode, $kode_barang)
    {
        $room = Room::where('kode_ruangan', $room_kode)->firstOrFail();
        $item = Inventaris::where('kode_barang', $kode_barang)->where('room_kode', $room_kode)->firstOrFail();
        $unitPecahan = DetailInventaris::where('id_barang', $kode_barang)->get();

        return view("pages.{$lokasi}.inventaris.detail", compact('lokasi', 'room', 'item', 'unitPecahan'));
    }

    public function print($lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) { abort(404); }
        $dataInventaris = Inventaris::where('room_kode', $room->kode_ruangan)->orderBy('nama_barang', 'asc')->get();
        return view("pages.{$lokasi}.inventaris.print", compact('dataInventaris', 'lokasi', 'room'));
    }

    private function sendNotification($action, $namaBarang)
    {
        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), $action, 'Inventaris', $namaBarang));
        }
    }
}