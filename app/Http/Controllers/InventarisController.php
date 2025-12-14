<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User; // Import User model
use App\Notifications\DataModificationNotification; // Import Notification class
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Notification; // Import Notification facade

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
        $query = Inventaris::where('room_id', $room->id);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('merk_model', 'LIKE', "%{$search}%");
            });
        }
        
        $dataInventaris = $query->latest()->paginate(10);
        
        $allRooms = Room::where('lokasi', $lokasi)->orderBy('name')->get();

        return view("pages.{$lokasi}.inventaris.index", compact('dataInventaris', 'lokasi', 'room', 'search', 'allRooms'));
    }

    /**
     * Menampilkan form untuk membuat data inventaris baru.
     */
    public function create($lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) {
            abort(404);
        }
        return view("pages.{$lokasi}.inventaris.create", compact('lokasi', 'room'));
    }

    /**
     * Menyimpan data inventaris yang baru dibuat ke database.
     */
    public function store(Request $request, $lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) {
            abort(404);
        }

        $request->validate([
            'nibar'              => 'nullable|string|max:255',
        'nomor_register'     => 'nullable|string|max:255',
        'kode_barang'        => 'required|string|max:255',
        'nama_barang'        => 'required|string|max:255',
        'spesifikasi_barang' => 'nullable|string',
        'merk_tipe'          => 'nullable|string|max:255',
        'tahun_perolehan'    => 'required|digits:4|integer|min:1900', // Validasi untuk format tahun
        'jumlah'             => 'required|integer|min:0',
        'satuan'             => 'required|string|max:255',
        'keterangan'         => 'nullable|string',
        ]);
        
        $dataToStore = $request->all();
        $dataToStore['lokasi'] = $lokasi;
        $dataToStore['room_id'] = $room->id;
        
        $inventaris = Inventaris::create($dataToStore);

        // Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'ditambahkan', 'Inventaris', $inventaris->nama_barang));

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->id])
                         ->with('success', 'Data inventaris berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit data inventaris.
     */
    public function edit($lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_id !== $room->id) {
            abort(404);
        }
        return view("pages.{$lokasi}.inventaris.edit", compact('lokasi', 'room', 'inventari'));
    }

    /**
     * Memperbarui data inventaris di database.
     */
    public function update(Request $request, $lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_id !== $room->id) {
            abort(404);
        }

        $request->validate([
            'nibar'              => 'nullable|string|max:255',
        'nomor_register'     => 'nullable|string|max:255',
        'kode_barang'        => 'required|string|max:255',
        'nama_barang'        => 'required|string|max:255',
        'spesifikasi_barang' => 'nullable|string',
        'merk_tipe'          => 'nullable|string|max:255',
        'tahun_perolehan'    => 'required|digits:4|integer|min:1900', // Validasi untuk format tahun
        'jumlah'             => 'required|integer|min:0',
        'satuan'             => 'required|string|max:255',
        'keterangan'         => 'nullable|string',
        ]);
        
        $inventari->update($request->all());

        // Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'diperbarui', 'Inventaris', $inventari->nama_barang));

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->id])
                         ->with('success', 'Data inventaris berhasil diperbarui.');
    }

    /**
     * Menghapus data inventaris dari database.
     */
    public function destroy($lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_id !== $room->id) {
            abort(404);
        }
        
        $itemName = $inventari->nama_barang;
        $inventari->delete();

        // Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'dihapus', 'Inventaris', $itemName));

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->id])
                         ->with('success', 'Data inventaris berhasil dihapus.');
    }

    /**
     * Menghasilkan halaman untuk dicetak.
     */
    public function print($lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) {
            abort(404);
        }
        $dataInventaris = Inventaris::where('room_id', $room->id)->get();
        return view("pages.{$lokasi}.inventaris.print", compact('dataInventaris', 'lokasi', 'room'));
    }
    
    /**
     * Memindahkan inventaris ke ruangan lain.
     */
    public function move(Request $request, $lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_id !== $room->id) {
            abort(404);
        }

        $request->validate([
            'new_room_id' => 'required|exists:rooms,id',
        ]);

        $newRoom = Room::find($request->new_room_id);

        if ($newRoom->lokasi !== $lokasi) {
            return redirect()->back()->with('error', 'Tidak bisa memindahkan barang ke lokasi yang berbeda.');
        }

        $inventari->room_id = $request->new_room_id;
        $inventari->save();

        // Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), "dipindahkan ke ruang {$newRoom->name}", 'Inventaris', $inventari->nama_barang));

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->id])
                         ->with('success', 'Barang berhasil dipindahkan ke ruangan ' . $newRoom->name);
    }
}

