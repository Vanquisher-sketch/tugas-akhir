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
        // Laravel otomatis mencari $room berdasarkan kode_ruangan karena Primary Key di Model sudah diubah
        if ($room->lokasi !== $lokasi) {
            abort(404);
        }

        $search = $request->query('search');
        
        // REVISI: Menggunakan room_kode (string) bukan room_id
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

        return view("pages.{$lokasi}.inventaris.index", compact('dataInventaris', 'lokasi', 'room', 'search', 'allRooms'));
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
            'kode_barang'        => 'required|string|max:100|unique:inventaris,kode_barang', // PK harus unik
            'nama_barang'        => 'required|string|max:255',
            'spesifikasi_barang' => 'nullable|string',
            'merk_tipe'          => 'nullable|string|max:255',
            'tahun_perolehan'    => 'required|digits:4|integer|min:1900',
            'jumlah'             => 'required|integer|min:1',
            'satuan'             => 'required|string|max:255',
            'keterangan'         => 'nullable|string',
        ]);

        // REVISI: Menggunakan room_kode
        $validated['room_kode'] = $room->kode_ruangan;
        $validated['lokasi']    = $lokasi; 

        $inventaris = Inventaris::create($validated);

        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), 'ditambahkan', 'Inventaris', $inventaris->nama_barang));
        }

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])
            ->with('success', 'Data inventaris berhasil ditambahkan.');
    }

    /**
     * LOGIKA MUTASI PARSIAL
     */
    public function move(Request $request, $lokasi, Room $room, Inventaris $inventari)
    {
        // REVISI: Cek menggunakan room_kode
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) {
            abort(404);
        }

        $request->validate([
            'new_room_id' => 'required|exists:rooms,kode_ruangan', // Cek ke kolom kode_ruangan
            'qty_to_move' => 'required|integer|min:1',
        ]);

        $qtyPindah = (int) $request->qty_to_move;
        $newRoom = Room::findOrFail($request->new_room_id);

        if ($qtyPindah > $inventari->jumlah) {
            return redirect()->back()->with('error', "Gagal: Jumlah pindah ($qtyPindah) melebihi stok ({$inventari->jumlah}).");
        }

        try {
            DB::transaction(function () use ($inventari, $newRoom, $qtyPindah, $lokasi) {
                // 1. Kurangi jumlah di asal
                $inventari->decrement('jumlah', $qtyPindah);

                // 2. Cari barang dengan KODE_BARANG yang sama di ruangan tujuan
                $targetItem = Inventaris::where('room_kode', $newRoom->kode_ruangan)
                    ->where('kode_barang', $inventari->kode_barang)
                    ->first();

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

            $recipients = User::whereIn('role_id', [1, 2])->get();
            if ($recipients->count() > 0) {
                Notification::send($recipients, new DataModificationNotification(
                    Auth::user(), 
                    "memindahkan $qtyPindah unit ke {$newRoom->name}", 
                    'Inventaris', 
                    $inventari->nama_barang
                ));
            }

            return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])
                ->with('success', "Berhasil memindahkan $qtyPindah unit ke ruangan {$newRoom->name}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memindahkan barang: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }
        
        $validated = $request->validate([
            'nibar' => 'nullable', 
            'nomor_register' => 'nullable', 
            // Abaikan unik untuk kode_barang milik sendiri saat update
            'kode_barang' => 'required|string|unique:inventaris,kode_barang,' . $inventari->kode_barang . ',kode_barang',
            'nama_barang' => 'required', 
            'tahun_perolehan' => 'required|digits:4',
            'jumlah' => 'required|integer|min:0', 
            'satuan' => 'required'
        ]);

        $inventari->update($validated);

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])->with('success', 'Data diperbarui.');
    }

    public function destroy($lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }
        
        $inventari->delete();
        
        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])->with('success', 'Data dihapus.');
    }

    public function print($lokasi, Room $room)
    {
        // Pastikan keamanan lokasi dan ruangan
        if ($room->lokasi !== $lokasi) {
            abort(404);
        }

        // Ambil semua data inventaris di ruangan tersebut
        $dataInventaris = Inventaris::where('room_kode', $room->kode_ruangan)
            ->orderBy('nama_barang', 'asc')
            ->get();

        return view("pages.{$lokasi}.inventaris.print", compact('dataInventaris', 'lokasi', 'room'));
    }
}