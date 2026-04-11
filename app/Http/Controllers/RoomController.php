<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DataModificationNotification;

class RoomController extends Controller
{
    /**
     * Menampilkan daftar data ruangan berdasarkan lokasi dan pencarian.
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->get('search');

        $dataRuangan = Room::where('lokasi', $lokasi)
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('kode_ruangan', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        $allRooms = Room::where('lokasi', $lokasi)
            ->select('name')
            ->get();

        return view("pages.{$lokasi}.room.index", compact('dataRuangan', 'allRooms', 'lokasi', 'search'));
    }

    /**
     * Menampilkan form untuk membuat data ruangan baru.
     */
    public function create($lokasi)
    {
        return view("pages.{$lokasi}.room.create", compact('lokasi'));
    }

    /**
     * Menyimpan data ruangan baru ke database.
     */
    public function store(Request $request, $lokasi)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Sekarang wajib unik di tabel rooms pada kolom kode_ruangan
            'kode_ruangan' => 'nullable|string|max:50|unique:rooms,kode_ruangan',
        ]);

        // LOGIKA AUTO-GENERATE KODE (Jika user tidak mengisi di form)
        $kode = $request->kode_ruangan;
        if (!$kode) {
            // Contoh: TAWANG-ADMIN-123
            $prefix = strtoupper(substr($lokasi, 0, 3));
            $nameSlug = strtoupper(str_replace(' ', '-', $request->name));
            $random = rand(100, 999);
            $kode = "{$prefix}-{$nameSlug}-{$random}";
        }

        $room = Room::create([
            'name'         => $request->name,
            'kode_ruangan' => $kode,
            'lokasi'       => $lokasi,
        ]);

        // Notifikasi
        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), 'ditambahkan', 'Ruangan', $room->name));
        }

        return redirect()->route('lokasi.room.index', ['lokasi' => $lokasi])
                         ->with('success', "Data ruangan {$room->name} berhasil ditambahkan dengan kode: {$kode}");
    }

    /**
     * Menampilkan form untuk mengedit data ruangan.
     */
    public function edit($lokasi, Room $room)
    {
        // Laravel otomatis mencari berdasarkan kode_ruangan karena sudah diset di Model
        if ($room->lokasi !== $lokasi) {
            abort(404, 'Data ruangan tidak ditemukan di lokasi ini.');
        }

        return view("pages.{$lokasi}.room.edit", compact('room', 'lokasi'));
    }

    /**
     * Memperbarui data ruangan di database.
     */
    public function update(Request $request, $lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) {
            abort(404, 'Data ruangan tidak ditemukan di lokasi ini.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            // Validasi unik kecuali untuk data ini sendiri (agar bisa disave tanpa ganti kode)
            'kode_ruangan' => "required|string|max:50|unique:rooms,kode_ruangan,{$room->kode_ruangan},kode_ruangan",
        ]);
        
        // Update manual karena Primary Key bukan 'id'
        $room->name = $request->name;
        $room->kode_ruangan = $request->kode_ruangan;
        $room->save();

        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), 'diperbarui', 'Ruangan', $room->name));
        }

        return redirect()->route('lokasi.room.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data ruangan berhasil diperbarui.');
    }

    /**
     * Menghapus data ruangan dari database.
     */
    public function destroy($lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) {
            abort(404, 'Data ruangan tidak ditemukan di lokasi ini.');
        }
        
        $roomName = $room->name; 
        $room->delete();

        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), 'dihapus', 'Ruangan', $roomName));
        }

        return redirect()->route('lokasi.room.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data ruangan berhasil dihapus.');
    }

    /**
     * Menghasilkan halaman untuk dicetak (print).
     */
    public function print($lokasi)
    {
        $dataRuangan = Room::where('lokasi', $lokasi)->latest()->get();
        return view("pages.{$lokasi}.room.print", compact('dataRuangan', 'lokasi'));
    }
}