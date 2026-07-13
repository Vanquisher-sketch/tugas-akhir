<?php

namespace App\Http\Controllers;

use App\Models\Ruangan; // 🌟 Menggunakan Model yang baru
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DataModificationNotification;

class RuanganController extends Controller
{
    /**
     * Menampilkan daftar data ruangan berdasarkan lokasi dan pencarian.
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->get('search');

        // Mengambil data berdasarkan filter lokasi dan pencarian
        $dataRuangan = Ruangan::where('lokasi', $lokasi)
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('ruangan_nama', 'LIKE', "%{$search}%")
                      ->orWhere('kode_ruangan', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        // Digunakan untuk dropdown filter atau opsi cetak
        $allRooms = Ruangan::where('lokasi', $lokasi)
            ->select('ruangan_nama')
            ->get();

        // 🌟 DIKEMBALIKAN: Memanggil view sesuai folder lokasi
        return view("pages.{$lokasi}.ruangan.index", compact('dataRuangan', 'allRooms', 'lokasi', 'search'));
    }

    /**
     * Menampilkan form untuk membuat data ruangan baru.
     */
    public function create($lokasi)
    {
        // 🌟 DIKEMBALIKAN: Memanggil view sesuai folder lokasi
        return view("pages.{$lokasi}.ruangan.create", compact('lokasi'));
    }

    /**
     * Menyimpan data ruangan baru ke database.
     */
    public function store(Request $request, $lokasi)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ruangans,ruangan_nama,NULL,kode_ruangan,lokasi,' . $lokasi,
        ]);

        // 🌟 LOGIKA AUTO-GENERATE KODE (Format: RP-01)
        
        // 1. Hitung urutan ruangan di kelurahan ini
        $jumlahRuangan = Ruangan::where('lokasi', $lokasi)->count();
        $noUrut = $jumlahRuangan + 1;

        // 2. Batasi maksimal 15 ruangan sesuai kesepakatan
        if ($noUrut > 15) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data! Batas maksimal 15 ruangan untuk kelurahan ini telah tercapai.');
        }

        // 3. Buat singkatan nama (Ambil huruf pertama dari setiap kata)
        $kata = explode(' ', $request->name);
        $singkatan = '';
        foreach ($kata as $k) {
            if (!empty($k)) {
                $singkatan .= strtoupper(substr($k, 0, 1));
            }
        }

        // 4. Format nomor menjadi 2 digit (01, 02, ..., 15)
        $nomorFormat = str_pad($noUrut, 2, '0', STR_PAD_LEFT);
        
        // 5. Gabungkan menjadi kode final (tanpa spasi agar aman untuk URL & Barcode)
        $kode = "{$singkatan}-{$nomorFormat}";

        // Cek jika kebetulan kodenya bentrok 
        if (Ruangan::where('kode_ruangan', $kode)->exists()) {
            $kode = "{$singkatan}-{$nomorFormat}-" . rand(10, 99);
        }

        $room = Ruangan::create([
            'ruangan_nama' => $request->name,
            'kode_ruangan' => $kode,
            'lokasi'       => $lokasi,
        ]);

        // Notifikasi ke Role 1 (Admin) dan Role 2 (Kecamatan)
        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), 'ditambahkan', 'Ruangan', $room->ruangan_nama));
        }

        return redirect()->route('lokasi.ruangan.index', ['lokasi' => $lokasi])
                         ->with('success', "Data ruangan {$room->ruangan_nama} berhasil ditambahkan dengan kode: {$kode}");
    }

    /**
     * Menampilkan form untuk mengedit data ruangan.
     */
    public function edit($lokasi, $kode_ruangan)
    {
        $room = Ruangan::findOrFail($kode_ruangan);

        if ($room->lokasi !== $lokasi) {
            abort(404, 'Data ruangan tidak ditemukan di lokasi ini.');
        }

        // 🌟 DIKEMBALIKAN: Memanggil view sesuai folder lokasi
        return view("pages.{$lokasi}.ruangan.edit", compact('room', 'lokasi'));
    }

    /**
     * Memperbarui data ruangan di database.
     */
    public function update(Request $request, $lokasi, $kode_ruangan)
    {
        $room = Ruangan::findOrFail($kode_ruangan);

        if ($room->lokasi !== $lokasi) {
            abort(404, 'Data ruangan tidak ditemukan di lokasi ini.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:ruangans,ruangan_nama,NULL,kode_ruangan,lokasi,' . $lokasi,
        ]);
        
        // Eksekusi update
        $room->update([
            'ruangan_nama' => $request->name,
        ]);

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), 'diperbarui', 'Ruangan', $room->ruangan_nama));
        }

        return redirect()->route('lokasi.ruangan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data ruangan berhasil diperbarui.');
    }

    /**
     * Menghapus data ruangan dari database.
     */
    public function destroy($lokasi, $kode_ruangan)
    {
        $room = Ruangan::findOrFail($kode_ruangan);

        if ($room->lokasi !== $lokasi) {
            abort(404, 'Data ruangan tidak ditemukan di lokasi ini.');
        }
        
        $roomName = $room->ruangan_nama; 
        $room->delete();

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), 'dihapus', 'Ruangan', $roomName));
        }

        return redirect()->route('lokasi.ruangan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data ruangan berhasil dihapus.');
    }

    /**
     * Menghasilkan halaman untuk dicetak (print).
     */
    public function print($lokasi)
    {
        $dataRuangan = Ruangan::where('lokasi', $lokasi)->latest()->get();
        // 🌟 DIKEMBALIKAN: Memanggil view sesuai folder lokasi
        return view("pages.{$lokasi}.ruangan.print", compact('dataRuangan', 'lokasi'));
    }
}