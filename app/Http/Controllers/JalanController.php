<?php

namespace App\Http\Controllers;

use App\Models\Jalan; // Pastikan Anda membuat model Jalan
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User; // Import User model
use App\Notifications\DataModificationNotification; // Import Notification class
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Notification; // Import Notification facade

class JalanController extends Controller
{
    /**
     * Menampilkan daftar data jalan/irigasi/jaringan berdasarkan lokasi dan pencarian.
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');

        $query = Jalan::where('lokasi', $lokasi);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('jenis_barang', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_register', 'LIKE', "%{$search}%")
                  ->orWhere('letak_lokasi', 'LIKE', "%{$search}%");
            });
        }
        
        $dataJalan = $query->latest()->paginate(10);
        
        return view("pages.{$lokasi}.jalan.index", compact('dataJalan', 'lokasi', 'search'));
    }

    /**
     * Menampilkan form untuk membuat data baru.
     */
    public function create($lokasi)
    {
        return view("pages.{$lokasi}.jalan.create", compact('lokasi'));
    }

    /**
     * Menyimpan data baru ke database.
     */
    public function store(Request $request, $lokasi)
    {
        $request->validate([
            'kode_barang'                     => 'required|string|max:255',
        'nama_barang'                     => 'required|string|max:255',
        'nibar'                           => 'nullable|string|max:255',
        'nomor_register'                  => 'required|string|max:255',
        'spesifikasi_barang'              => 'nullable|string',
        'spesifikasi_lainnya'             => 'nullable|string',
        'nomor_ruas_jalan_jembatan_irigasi' => 'nullable|string|max:255',
        'Lok'                          => 'required|string', // Kolom 'Lokasi' kedua (Sesuai Kolom 13)
        'titik_koordinat'                 => 'nullable|string|max:255',
        'status_kepemilikan_tanah'        => 'nullable|string|max:255',
        'jumlah'                          => 'required|integer|min:0',
        'satuan'                          => 'required|string|max:255',
        'harga_satuan'                    => 'required|numeric|min:0',
        'nilai_perolehan'                 => 'required|numeric|min:0',
        'cara_perolehan'                  => 'required|string|max:255',
        'tanggal_perolehan'               => 'required|date',
        'status_penggunaan'               => 'nullable|string|max:255',
        'keterangan'                      => 'nullable|string',
        ]);
        
        $dataToStore = $request->all();
        $dataToStore['lokasi'] = $lokasi;
        
        $jalan = Jalan::create($dataToStore);

        // Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'ditambahkan', 'Jalan, Irigasi & Jaringan', $jalan->jenis_barang));

        return redirect()->route('lokasi.jalan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Jalan, Irigasi & Jaringan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit data.
     */
    public function edit($lokasi, Jalan $jalan)
    {
        if ($jalan->lokasi !== $lokasi) {
            abort(404, 'Data tidak ditemukan di lokasi ini.');
        }
        return view("pages.{$lokasi}.jalan.edit", compact('jalan', 'lokasi'));
    }

    /**
     * Memperbarui data di database.
     */
    public function update(Request $request, $lokasi, Jalan $jalan)
    {
        if ($jalan->lokasi !== $lokasi) {
            abort(404, 'Data tidak ditemukan di lokasi ini.');
        }

        $request->validate([
            'kode_barang'                     => 'required|string|max:255',
        'nama_barang'                     => 'required|string|max:255',
        'nibar'                           => 'nullable|string|max:255',
        'nomor_register'                  => 'required|string|max:255',
        'spesifikasi_barang'              => 'nullable|string',
        'spesifikasi_lainnya'             => 'nullable|string',
        'nomor_ruas_jalan_jembatan_irigasi' => 'nullable|string|max:255',
        'Lok'                          => 'required|string', // Kolom 'Lokasi' kedua (Sesuai Kolom 13)
        'titik_koordinat'                 => 'nullable|string|max:255',
        'status_kepemilikan_tanah'        => 'nullable|string|max:255',
        'jumlah'                          => 'required|integer|min:0',
        'satuan'                          => 'required|string|max:255',
        'harga_satuan'                    => 'required|numeric|min:0',
        'nilai_perolehan'                 => 'required|numeric|min:0',
        'cara_perolehan'                  => 'required|string|max:255',
        'tanggal_perolehan'               => 'required|date',
        'status_penggunaan'               => 'nullable|string|max:255',
        'keterangan'                      => 'nullable|string',
        ]);
        
        $jalan->update($request->all());

        // Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'diperbarui', 'Jalan, Irigasi & Jaringan', $jalan->jenis_barang));

        return redirect()->route('lokasi.jalan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Jalan, Irigasi & Jaringan berhasil diperbarui.');
    }

    /**
     * Menghapus data dari database.
     */
    public function destroy($lokasi, Jalan $jalan)
    {
        if ($jalan->lokasi !== $lokasi) {
            abort(404, 'Data tidak ditemukan di lokasi ini.');
        }
        
        $itemName = $jalan->jenis_barang;
        $jalan->delete();

        // Kirim notifikasi ke Admin (1) dan Kecamatan (2)
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'dihapus', 'Jalan, Irigasi & Jaringan', $itemName));

        return redirect()->route('lokasi.jalan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Jalan, Irigasi & Jaringan berhasil dihapus.');
    }

    /**
     * Menghasilkan halaman untuk dicetak (print).
     */
    public function print($lokasi)
    {
        $dataJalan = Jalan::where('lokasi', $lokasi)->latest()->get();
        return view("pages.{$lokasi}.jalan.print", compact('dataJalan', 'lokasi'));
    }
}

