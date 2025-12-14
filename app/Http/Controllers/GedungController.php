<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User; // Import User model
use App\Notifications\DataModificationNotification; // Import Notification class
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Notification; // Import Notification facade

class GedungController extends Controller
{
    /**
     * Menampilkan daftar data gedung berdasarkan lokasi dan pencarian.
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        $query = Gedung::where('lokasi', $lokasi);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('jenis_barang', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('letak_lokasi', 'LIKE', "%{$search}%");
            });
        }
        
        $dataGedung = $query->latest()->paginate(10);
        return view("pages.{$lokasi}.gedung.index", compact('dataGedung', 'lokasi', 'search'));
    }

    /**
     * Menampilkan form untuk membuat data gedung baru.
     */
    public function create($lokasi)
    {
        return view("pages.{$lokasi}.gedung.create", compact('lokasi'));
    }

    /**
     * Menyimpan data gedung yang baru dibuat ke database.
     */
    public function store(Request $request, $lokasi)
    {
        $request->validate([
            'kode_barang'               => 'required|string|max:255',
        'nama_barang'               => 'required|string|max:255',
        'nbar'                      => 'nullable|string|max:255',
        'nomor_register'            => 'required|string|max:255',
        'spesifikasi_barang'        => 'nullable|string',
        'spesifikasi_lainnya'       => 'nullable|string',
        'jumlah_lantai'             => 'nullable|integer|min:0',
        'Lok'                    => 'required|string', // Kolom 'Lokasi' kedua (Sesuai Kolom 13)
        'titik_koordinat'           => 'nullable|string|max:255',
        'status_kepemilikan_tanah'  => 'nullable|string|max:255',
        'jumlah'                    => 'required|integer|min:0',
        'satuan'                    => 'required|string|max:255',
        'harga_satuan'              => 'required|numeric|min:0',
        'nilai_perolehan'           => 'required|numeric|min:0',
        'cara_perolehan'            => 'required|string|max:255',
        'tanggal_perolehan'         => 'required|date',
        'status_penggunaan'         => 'nullable|string|max:255',
        'keterangan'                => 'nullable|string'
        ]);
        
        $dataToStore = $request->all();
        $dataToStore['lokasi'] = $lokasi;
        
        $gedung = Gedung::create($dataToStore);

        // REVISI: Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'ditambahkan', 'Gedung & Bangunan', $gedung->jenis_barang));

        return redirect()->route('lokasi.gedung.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data gedung & bangunan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit data gedung.
     */
    public function edit($lokasi, Gedung $gedung)
    {
        if ($gedung->lokasi !== $lokasi) { abort(404); }
        return view("pages.{$lokasi}.gedung.edit", compact('gedung', 'lokasi'));
    }

    /**
     * Memperbarui data gedung di database.
     */
    public function update(Request $request, $lokasi, Gedung $gedung)
    {
        if ($gedung->lokasi !== $lokasi) { abort(404); }

        $request->validate([
            'kode_barang'               => 'required|string|max:255',
        'nama_barang'               => 'required|string|max:255',
        'nbar'                      => 'nullable|string|max:255',
        'nomor_register'            => 'required|string|max:255',
        'spesifikasi_barang'        => 'nullable|string',
        'spesifikasi_lainnya'       => 'nullable|string',
        'jumlah_lantai'             => 'nullable|integer|min:0',
        'Lok'                    => 'required|string', // Kolom 'Lokasi' kedua (Sesuai Kolom 13)
        'titik_koordinat'           => 'nullable|string|max:255',
        'status_kepemilikan_tanah'  => 'nullable|string|max:255',
        'jumlah'                    => 'required|integer|min:0',
        'satuan'                    => 'required|string|max:255',
        'harga_satuan'              => 'required|numeric|min:0',
        'nilai_perolehan'           => 'required|numeric|min:0',
        'cara_perolehan'            => 'required|string|max:255',
        'tanggal_perolehan'         => 'required|date',
        'status_penggunaan'         => 'nullable|string|max:255',
        'keterangan'                => 'nullable|string'
        ]);
        
        $gedung->update($request->all());

        // REVISI: Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'diperbarui', 'Gedung & Bangunan', $gedung->jenis_barang));

        return redirect()->route('lokasi.gedung.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data gedung & bangunan berhasil diperbarui.');
    }

    /**
     * Menghapus data gedung dari database.
     */
    public function destroy($lokasi, Gedung $gedung)
    {
        if ($gedung->lokasi !== $lokasi) { abort(404); }
        
        $itemName = $gedung->jenis_barang; // Simpan nama sebelum dihapus
        $gedung->delete();
        
        // REVISI: Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'dihapus', 'Gedung & Bangunan', $itemName));

        return redirect()->route('lokasi.gedung.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data gedung & bangunan berhasil dihapus.');
    }

    /**
     * Menghasilkan halaman untuk dicetak.
     */
    public function print($lokasi)
    {
        $dataGedung = Gedung::where('lokasi', $lokasi)->latest()->get();
        return view("pages.{$lokasi}.gedung.print", compact('dataGedung', 'lokasi'));
    }
}

