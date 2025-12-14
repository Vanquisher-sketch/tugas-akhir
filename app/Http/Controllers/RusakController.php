<?php

namespace App\Http\Controllers;

use App\Models\Rusak; // Pastikan Anda membuat model Rusak
use Illuminate\Http\Request;
use App\Models\User; // Import User model
use App\Notifications\DataModificationNotification; // Import Notification class
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Notification; // Import Notification facade

class RusakController extends Controller
{
    /**
     * Menampilkan daftar data barang rusak berdasarkan lokasi dan pencarian.
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');

        $query = Rusak::where('lokasi', $lokasi);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('id_pemda', 'LIKE', "%{$search}%") // Menggunakan id_pemda
                  ->orWhere('spesifikasi', 'LIKE', "%{$search}%")
                  ->orWhere('no_polisi', 'LIKE', "%{$search}%");
            });
        }
        
        $dataRusak = $query->latest()->paginate(10);
        
        return view("pages.{$lokasi}.rusak.index", compact('dataRusak', 'lokasi', 'search'));
    }

    /**
     * Menampilkan form untuk membuat data baru.
     */
    public function create($lokasi)
    {
        return view("pages.{$lokasi}.rusak.create", compact('lokasi'));
    }

    /**
     * Menyimpan data baru ke database.
     */
    public function store(Request $request, $lokasi)
    {
        // REVISI: Validasi disesuaikan
        $request->validate([
            'id_pemda' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string|max:255',
            'no_polisi' => 'nullable|string|max:255',
            'tahun_perolehan' => 'required|digits:4',
            'harga_perolehan' => 'required|numeric',
            'kondisi' => 'required|string|max:255',
            'tercatat_di_kib' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);
        
        $dataToStore = $request->all();
        $dataToStore['lokasi'] = $lokasi;
        
        $rusak = Rusak::create($dataToStore);

        // Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'ditambahkan', 'Barang Rusak', $rusak->nama_barang));

        return redirect()->route('lokasi.rusak.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Barang Rusak berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit data.
     */
    public function edit($lokasi, Rusak $rusak)
    {
        if ($rusak->lokasi !== $lokasi) {
            abort(404, 'Data tidak ditemukan di lokasi ini.');
        }
        return view("pages.{$lokasi}.rusak.edit", compact('rusak', 'lokasi'));
    }

    /**
     * Memperbarui data di database.
     */
    public function update(Request $request, $lokasi, Rusak $rusak)
    {
        if ($rusak->lokasi !== $lokasi) {
            abort(404, 'Data tidak ditemukan di lokasi ini.');
        }

        // REVISI: Validasi disesuaikan
        $request->validate([
            'id_pemda' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string|max:255',
            'no_polisi' => 'nullable|string|max:255',
            'tahun_perolehan' => 'required|digits:4',
            'harga_perolehan' => 'required|numeric',
            'kondisi' => 'required|string|max:255',
            'tercatat_di_kib' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);
        
        $rusak->update($request->all());

        // Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'diperbarui', 'Barang Rusak', $rusak->nama_barang));

        return redirect()->route('lokasi.rusak.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Barang Rusak berhasil diperbarui.');
    }

    /**
     * Menghapus data dari database.
     */
    public function destroy($lokasi, Rusak $rusak)
    {
        if ($rusak->lokasi !== $lokasi) {
            abort(404, 'Data tidak ditemukan di lokasi ini.');
        }
        
        $itemName = $rusak->nama_barang;
        $rusak->delete();

        // Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'dihapus', 'Barang Rusak', $itemName));

        return redirect()->route('lokasi.rusak.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Barang Rusak berhasil dihapus.');
    }

    /**
     * Menghasilkan halaman untuk dicetak (print).
     */
    public function print($lokasi)
    {
        $dataRusak = Rusak::where('lokasi', $lokasi)->latest()->get();
        return view("pages.{$lokasi}.rusak.print", compact('dataRusak', 'lokasi'));
    }
}

