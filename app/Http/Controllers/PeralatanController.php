<?php

namespace App\Http\Controllers;

use App\Models\Peralatan;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class PeralatanController extends Controller
{
    /**
     * Menampilkan daftar peralatan (KIB B).
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        $query = Peralatan::where('lokasi', $lokasi);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('nibr', 'LIKE', "%{$search}%")       // Sesuai Model: nibr
                  ->orWhere('merk_tipe', 'LIKE', "%{$search}%")  // Sesuai Model: merk_tipe
                  ->orWhere('nomor_polisi', 'LIKE', "%{$search}%");
            });
        }
        
        $dataPeralatan = $query->latest()->paginate(10);
        
        return view("pages.{$lokasi}.peralatan.index", compact('dataPeralatan', 'lokasi', 'search'));
    }

    /**
     * Form Tambah Data.
     */
    public function create($lokasi)
    {
        return view("pages.{$lokasi}.peralatan.create", compact('lokasi'));
    }

    /**
     * Simpan Data (STORE).
     */
    public function store(Request $request, $lokasi)
    {
        // 1. BERSIHKAN FORMAT RUPIAH & ANGKA
        $inputs = $request->all();
        $currencyFields = ['harga_satuan', 'nilai_perolehan', 'jumlah'];

        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $inputs[$field] = preg_replace('/[^0-9]/', '', $inputs[$field]);
            }
        }
        $request->replace($inputs);

        // 2. VALIDASI DATA SESUAI MODEL
        $validator = Validator::make($request->all(), [
            // Identitas
            'kode_barang'       => 'required|string|max:255',
            'nama_barang'       => 'required|string|max:255',
            'nibr'              => 'nullable|string|max:255', // Sesuai Model
            'nomor_register'    => 'nullable|string|max:255',
            
            // Lokasi (Wajib L Besar sesuai Model)
            'Lok'               => 'required|string', 

            // Spesifikasi
            'merk_tipe'         => 'nullable|string|max:255', // Sesuai Model
            'spesifikasi_barang'=> 'nullable|string',
            'spesifikasi_lainnya'=> 'nullable|string',

            // Nomor Kendaraan (Hanya 3 ini yg ada di Model)
            'nomor_rangka'      => 'nullable|string|max:255',
            'nomor_polisi'      => 'nullable|string|max:255',
            'nomor_bpkb'        => 'nullable|string|max:255',

            // Nilai & Perolehan
            'cara_perolehan'    => 'required|string|max:255',
            'tanggal_perolehan' => 'required|date',
            'harga_satuan'      => 'required|numeric|min:0',
            'nilai_perolehan'   => 'required|numeric|min:0',
            'jumlah'            => 'required|integer|min:1',
            'satuan'            => 'required|string|max:50',

            // Status
            'status_penggunaan' => 'required|string|max:255',
            'keterangan'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 3. SIAPKAN DATA
        $dataToStore = $validator->validated();
        $dataToStore['lokasi'] = $lokasi;

        // 4. SIMPAN
        $peralatan = Peralatan::create($dataToStore);

        // 5. NOTIFIKASI
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(
            Auth::user(), 'ditambahkan', 'Peralatan (KIB B)', $peralatan->nama_barang
        ));

        return redirect()->route('lokasi.peralatan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Peralatan berhasil ditambahkan.');
    }

    /**
     * Form Edit.
     */
    public function edit($lokasi, Peralatan $peralatan)
    {
        if ($peralatan->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.peralatan.edit", compact('peralatan', 'lokasi'));
    }

    /**
     * Update Data.
     */
    public function update(Request $request, $lokasi, Peralatan $peralatan)
    {
        if ($peralatan->lokasi !== $lokasi) abort(404);

        // 1. BERSIHKAN FORMAT
        $inputs = $request->all();
        $currencyFields = ['harga_satuan', 'nilai_perolehan', 'jumlah'];
        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $inputs[$field] = preg_replace('/[^0-9]/', '', $inputs[$field]);
            }
        }
        $request->replace($inputs);

        // 2. VALIDASI
        $validator = Validator::make($request->all(), [
            'kode_barang'       => 'required|string|max:255',
            'nama_barang'       => 'required|string|max:255',
            'nibr'              => 'nullable|string|max:255',
            'nomor_register'    => 'nullable|string|max:255',
            'Lok'               => 'required|string', 

            'merk_tipe'         => 'nullable|string|max:255',
            'spesifikasi_barang'=> 'nullable|string',
            'spesifikasi_lainnya'=> 'nullable|string',

            'nomor_rangka'      => 'nullable|string|max:255',
            'nomor_polisi'      => 'nullable|string|max:255',
            'nomor_bpkb'        => 'nullable|string|max:255',

            'cara_perolehan'    => 'required|string',
            'tanggal_perolehan' => 'required|date',
            'harga_satuan'      => 'required|numeric|min:0',
            'nilai_perolehan'   => 'required|numeric|min:0',
            'jumlah'            => 'required|integer|min:1',
            'satuan'            => 'required|string',
            'status_penggunaan' => 'required|string',
            'keterangan'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 3. UPDATE
        $peralatan->update($validator->validated());

        // 4. NOTIFIKASI
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(
            Auth::user(), 'diperbarui', 'Peralatan (KIB B)', $peralatan->nama_barang
        ));

        return redirect()->route('lokasi.peralatan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Hapus Data.
     */
    public function destroy($lokasi, Peralatan $peralatan)
    {
        if ($peralatan->lokasi !== $lokasi) abort(404);

        $namaBarang = $peralatan->nama_barang;
        $peralatan->delete();

        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(
            Auth::user(), 'dihapus', 'Peralatan (KIB B)', $namaBarang
        ));

        return redirect()->route('lokasi.peralatan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data berhasil dihapus.');
    }

    public function print($lokasi)
    {
        $dataPeralatan = Peralatan::where('lokasi', $lokasi)->latest()->get();
        return view("pages.{$lokasi}.peralatan.print", compact('dataPeralatan', 'lokasi'));
    }
}