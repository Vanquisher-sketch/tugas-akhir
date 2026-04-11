<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class GedungController extends Controller
{
    /**
     * Menampilkan daftar data gedung (KIB C).
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        $query = Gedung::where('lokasi', $lokasi);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('Lok', 'LIKE', "%{$search}%");
            });
        }
        
        $dataGedung = $query->latest('updated_at')->paginate(10);
        return view("pages.{$lokasi}.gedung.index", compact('dataGedung', 'lokasi', 'search'));
    }

    /**
     * Fitur Saran Pencarian (Autocomplete) - AJAX
     */
    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        
        $results = Gedung::where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('Lok', 'LIKE', "%{$search}%");
            })
            ->limit(5)
            ->get(['nama_barang as label', 'kode_barang as value']);

        return response()->json($results);
    }

    public function create($lokasi)
    {
        return view("pages.{$lokasi}.gedung.create", compact('lokasi'));
    }

    /**
     * Menyimpan data gedung baru.
     */
    public function store(Request $request, $lokasi)
    {
        // 1. BERSIHKAN FORMAT RUPIAH & RIBUAN
        $inputs = $request->all();
        $currencyFields = ['harga_satuan', 'nilai_perolehan', 'jumlah'];

        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        // 2. VALIDASI
        $validator = Validator::make($request->all(), [
            'kode_barang'               => 'required|string|max:100|unique:gedungs,kode_barang',
            'nama_barang'               => 'required|string|max:255',
            'nbar'                      => 'nullable|string|max:255',
            'nomor_register'            => 'required|string|max:255',
            'spesifikasi_barang'        => 'nullable|string',
            'spesifikasi_lainnya'       => 'nullable|string',
            'jumlah_lantai'             => 'nullable|integer|min:0',
            'Lok'                       => 'required|string', 
            'titik_koordinat'           => 'nullable|string|max:255',
            'status_kepemilikan_tanah'  => 'nullable|string|max:255',
            'jumlah'                    => 'required|numeric|min:0',
            'satuan'                    => 'required|string|max:255',
            'harga_satuan'              => 'required|numeric|min:0',
            'nilai_perolehan'           => 'required|numeric|min:0',
            'cara_perolehan'            => 'required|string|max:255',
            'tanggal_perolehan'         => 'required|date',
            'status_penggunaan'         => 'nullable|string|max:255',
            'keterangan'                => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $dataToStore = $validator->validated();
        $dataToStore['lokasi'] = $lokasi;
        
        $gedung = Gedung::create($dataToStore);

        // 3. NOTIFIKASI
        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'ditambahkan', 'Gedung & Bangunan', $gedung->nama_barang
            ));
        }

        return redirect()->route('lokasi.gedung.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Gedung & Bangunan berhasil ditambahkan.');
    }

    public function edit($lokasi, Gedung $gedung)
    {
        if ($gedung->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.gedung.edit", compact('gedung', 'lokasi'));
    }

    /**
     * Memperbarui data gedung.
     */
    public function update(Request $request, $lokasi, Gedung $gedung)
    {
        if ($gedung->lokasi !== $lokasi) abort(404);

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

        // 2. VALIDASI (Kecualikan kode milik sendiri)
        $validator = Validator::make($request->all(), [
            'kode_barang'               => "required|string|max:100|unique:gedungs,kode_barang,{$gedung->kode_barang},kode_barang",
            'nama_barang'               => 'required|string|max:255',
            'nbar'                      => 'nullable|string|max:255',
            'nomor_register'            => 'required|string|max:255',
            'spesifikasi_barang'        => 'nullable|string',
            'spesifikasi_lainnya'       => 'nullable|string',
            'jumlah_lantai'             => 'nullable|integer|min:0',
            'Lok'                       => 'required|string', 
            'titik_koordinat'           => 'nullable|string|max:255',
            'status_kepemilikan_tanah'  => 'nullable|string|max:255',
            'jumlah'                    => 'required|numeric|min:0',
            'satuan'                    => 'required|string|max:255',
            'harga_satuan'              => 'required|numeric|min:0',
            'nilai_perolehan'           => 'required|numeric|min:0',
            'cara_perolehan'            => 'required|string|max:255',
            'tanggal_perolehan'         => 'required|date',
            'status_penggunaan'         => 'nullable|string|max:255',
            'keterangan'                => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $gedung->update($validator->validated());

        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'diperbarui', 'Gedung & Bangunan', $gedung->nama_barang
            ));
        }

        return redirect()->route('lokasi.gedung.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Gedung & Bangunan berhasil diperbarui.');
    }

    public function destroy($lokasi, Gedung $gedung)
    {
        if ($gedung->lokasi !== $lokasi) abort(404);
        
        $itemName = $gedung->nama_barang;
        $gedung->delete();
        
        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'dihapus', 'Gedung & Bangunan', $itemName
            ));
        }

        return redirect()->route('lokasi.gedung.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Gedung & Bangunan berhasil dihapus.');
    }

    public function print($lokasi)
    {
        $dataGedung = Gedung::where('lokasi', $lokasi)->latest('updated_at')->get();
        return view("pages.{$lokasi}.gedung.print", compact('dataGedung', 'lokasi'));
    }
}