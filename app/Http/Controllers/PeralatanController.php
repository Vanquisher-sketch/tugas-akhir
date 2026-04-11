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
                  ->orWhere('nibr', 'LIKE', "%{$search}%")
                  ->orWhere('merk_tipe', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_polisi', 'LIKE', "%{$search}%");
            });
        }
        
        $dataPeralatan = $query->latest('updated_at')->paginate(10);
        
        return view("pages.{$lokasi}.peralatan.index", compact('dataPeralatan', 'lokasi', 'search'));
    }

    public function create($lokasi)
    {
        return view("pages.{$lokasi}.peralatan.create", compact('lokasi'));
    }

    /**
     * Simpan Data (STORE).
     */
    public function store(Request $request, $lokasi)
    {
        // 1. BERSIHKAN FORMAT RUPIAH & RIBUAN
        $inputs = $request->all();
        $currencyFields = ['harga_satuan', 'nilai_perolehan', 'jumlah'];

        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                // Menghapus titik ribuan dan mengubah koma desimal menjadi titik
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        // 2. VALIDASI (Penting: kode_barang harus Unique)
        $validator = Validator::make($request->all(), [
            'kode_barang'        => 'required|string|max:100|unique:peralatans,kode_barang',
            'nama_barang'        => 'required|string|max:255',
            'nibr'               => 'nullable|string|max:255',
            'nomor_register'     => 'nullable|string|max:255',
            'Lok'                => 'required|string', 
            'merk_tipe'          => 'nullable|string|max:255',
            'spesifikasi_barang' => 'nullable|string',
            'spesifikasi_lainnya'=> 'nullable|string',
            'nomor_rangka'       => 'nullable|string|max:255',
            'nomor_polisi'       => 'nullable|string|max:255',
            'nomor_bpkb'         => 'nullable|string|max:255',
            'cara_perolehan'     => 'required|string|max:255',
            'tanggal_perolehan'  => 'required|date',
            'harga_satuan'       => 'required|numeric|min:0',
            'nilai_perolehan'    => 'required|numeric|min:0',
            'jumlah'             => 'required|integer|min:1',
            'satuan'             => 'required|string|max:50',
            'status_penggunaan'  => 'required|string|max:255',
            'keterangan'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $dataToStore = $validator->validated();
        $dataToStore['lokasi'] = $lokasi;

        $peralatan = Peralatan::create($dataToStore);

        // 3. NOTIFIKASI
        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'ditambahkan', 'Peralatan (KIB B)', $peralatan->nama_barang
            ));
        }

        return redirect()->route('lokasi.peralatan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Peralatan berhasil ditambahkan.');
    }

    public function edit($lokasi, Peralatan $peralatan)
    {
        if ($peralatan->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.peralatan.edit", compact('peralatan', 'lokasi'));
    }

    /**
     * Update Data Peralatan
     */
    public function update(Request $request, $lokasi, Peralatan $peralatan)
    {
        if ($peralatan->lokasi !== $lokasi) abort(404);

        // 1. BERSIHKAN FORMAT
        $inputs = $request->all();
        $currencyFields = ['harga_satuan', 'nilai_perolehan', 'jumlah'];
        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        // 2. VALIDASI (Kecualikan kode_barang milik sendiri)
        $validator = Validator::make($request->all(), [
            'kode_barang'        => "required|string|max:100|unique:peralatans,kode_barang,{$peralatan->kode_barang},kode_barang",
            'nama_barang'        => 'required|string|max:255',
            'nibr'               => 'nullable|string|max:255',
            'nomor_register'     => 'nullable|string|max:255',
            'Lok'                => 'required|string', 
            'merk_tipe'          => 'nullable|string|max:255',
            'spesifikasi_barang' => 'nullable|string',
            'spesifikasi_lainnya'=> 'nullable|string',
            'nomor_rangka'       => 'nullable|string|max:255',
            'nomor_polisi'       => 'nullable|string|max:255',
            'nomor_bpkb'         => 'nullable|string|max:255',
            'cara_perolehan'     => 'required|string',
            'tanggal_perolehan'  => 'required|date',
            'harga_satuan'       => 'required|numeric|min:0',
            'nilai_perolehan'    => 'required|numeric|min:0',
            'jumlah'             => 'required|integer|min:1',
            'satuan'             => 'required|string',
            'status_penggunaan'  => 'required|string',
            'keterangan'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $peralatan->update($validator->validated());

        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'diperbarui', 'Peralatan (KIB B)', $peralatan->nama_barang
            ));
        }

        return redirect()->route('lokasi.peralatan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Peralatan berhasil diperbarui.');
    }

    public function destroy($lokasi, Peralatan $peralatan)
    {
        if ($peralatan->lokasi !== $lokasi) abort(404);

        $namaBarang = $peralatan->nama_barang;
        $peralatan->delete();

        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'dihapus', 'Peralatan (KIB B)', $namaBarang
            ));
        }

        return redirect()->route('lokasi.peralatan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Peralatan berhasil dihapus.');
    }

    public function print($lokasi)
    {
        $dataPeralatan = Peralatan::where('lokasi', $lokasi)->latest('updated_at')->get();
        return view("pages.{$lokasi}.peralatan.print", compact('dataPeralatan', 'lokasi'));
    }
}