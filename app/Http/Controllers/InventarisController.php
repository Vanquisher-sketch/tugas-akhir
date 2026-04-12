<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Room;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class InventarisController extends Controller
{
    /**
     * Menampilkan daftar data inventaris untuk sebuah ruangan.
     */
    public function index(Request $request, $lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) {
            abort(404);
        }

        $search = $request->query('search');
        $query = Inventaris::where('room_kode', $room->kode_ruangan);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                    ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                    ->orWhere('merk_tipe', 'LIKE', "%{$search}%")
                    ->orWhere('nibar', 'LIKE', "%{$search}%");
            });
        }

        $dataInventaris = $query->latest()->paginate(10);
        $allRooms = Room::where('lokasi', $lokasi)->orderBy('name')->get();
        
        // Daftar Satuan untuk Dropdown
        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];

        return view("pages.{$lokasi}.inventaris.index", compact('dataInventaris', 'lokasi', 'room', 'search', 'allRooms', 'daftarSatuan'));
    }

    /**
     * Menampilkan form tambah inventaris (PENTING)
     */
    public function create($lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) {
            abort(404);
        }

        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];

        return view("pages.{$lokasi}.inventaris.create", compact('lokasi', 'room', 'daftarSatuan'));
    }

    /**
     * Menyimpan data inventaris baru.
     */
    public function store(Request $request, $lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) {
            abort(404);
        }

        $validated = $request->validate([
            'nibar'              => 'nullable|string|max:255',
            'nomor_register'     => 'nullable|string|max:255',
            'kode_barang'        => 'required|string|max:100|unique:inventaris,kode_barang', 
            'nama_barang'        => 'required|string|max:255',
            'spesifikasi_barang' => 'nullable|string',
            'merk_tipe'          => 'nullable|string|max:255',
            'tahun_perolehan'    => 'required|digits:4|integer|min:1900',
            'jumlah'             => 'required|integer|min:1',
            'satuan'             => 'required|string|in:Unit,Buah,Set,Meter,Lembar,Paket,Dus,Pcs',
            'keterangan'         => 'nullable|string',
        ]);

        $validated['room_kode'] = $room->kode_ruangan;
        $validated['lokasi']    = $lokasi; 

        $inventaris = Inventaris::create($validated);
        $this->sendNotification('ditambahkan', $inventaris->nama_barang);

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])
            ->with('success', 'Data inventaris berhasil disimpan.');
    }

    /**
     * Menampilkan form edit inventaris (PENTING)
     */
    public function edit($lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) {
            abort(404);
        }

        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];

        return view("pages.{$lokasi}.inventaris.edit", compact('lokasi', 'room', 'inventari', 'daftarSatuan'));
    }

    /**
     * Memperbarui data inventaris.
     */
    public function update(Request $request, $lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }
        
        $validated = $request->validate([
            'nibar' => 'nullable', 
            'nomor_register' => 'nullable', 
            'kode_barang' => 'required|string|unique:inventaris,kode_barang,' . $inventari->kode_barang . ',kode_barang',
            'nama_barang' => 'required', 
            'tahun_perolehan' => 'required|digits:4',
            'jumlah' => 'required|integer|min:0', 
            'satuan' => 'required|in:Unit,Buah,Set,Meter,Lembar,Paket,Dus,Pcs'
        ]);

        $inventari->update($validated);
        $this->sendNotification('diperbarui', $inventari->nama_barang);

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])->with('success', 'Data inventaris diperbarui.');
    }

    /**
     * LOGIKA MUTASI (Pindah Barang)
     */
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
            return redirect()->back()->with('error', "Gagal: Jumlah pindah melebihi stok.");
        }

        try {
            DB::transaction(function () use ($inventari, $newRoom, $qtyPindah, $lokasi) {
                $inventari->decrement('jumlah', $qtyPindah);
                $targetItem = Inventaris::where('room_kode', $newRoom->kode_ruangan)->where('kode_barang', $inventari->kode_barang)->first();

                if ($targetItem) {
                    $targetItem->increment('jumlah', $qtyPindah);
                } else {
                    $newItem = $inventari->replicate();
                    $newItem->room_kode = $newRoom->kode_ruangan;
                    $newItem->jumlah    = $qtyPindah;
                    $newItem->lokasi    = $lokasi; 
                    $newItem->save();
                }

                if ($inventari->fresh()->jumlah <= 0) {
                    $inventari->delete();
                }
            });

            $this->sendNotification("memindahkan $qtyPindah unit ke {$newRoom->name}", $inventari->nama_barang);
            return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])->with('success', 'Berhasil memindahkan barang.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memindahkan barang.');
        }
    }

    /**
     * Hapus Data (Masuk ke Arsip/Soft Delete)
     */
    public function destroy($lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }
        $namaBarang = $inventari->nama_barang;
        $inventari->delete(); 
        $this->sendNotification('dihapus (masuk arsip)', $namaBarang);
        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Cetak Kartu Inventaris Ruangan (KIR)
     */
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