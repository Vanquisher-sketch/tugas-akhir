<?php

namespace App\Http\Controllers;

use App\Models\Tanah;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class TanahController extends Controller
{
    /**
     * Menampilkan daftar KIB A (Tanah)
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        $query = Tanah::where('lokasi', $lokasi);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('Lok', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('bukti_nomor', 'LIKE', "%{$search}%");
            });
        }
        
        $dataTanah = $query->latest('updated_at')->paginate(10);
        return view("pages.{$lokasi}.tanah.index", compact('dataTanah', 'lokasi', 'search'));
    }

    /**
     * Fitur Saran Pencarian (Autocomplete) - AJAX
     */
    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        
        // Mengambil data tanah yang sesuai dengan input user
        $results = Tanah::where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%") // Ditambah pencarian kode
                  ->orWhere('Lok', 'LIKE', "%{$search}%");
            })
            ->limit(5)
            ->get(['nama_barang as label', 'kode_barang as value']);

        return response()->json($results);
    }

    public function create($lokasi)
    {
        return view("pages.{$lokasi}.tanah.create", compact('lokasi'));
    }

    /**
     * Simpan Data Tanah
     */
    public function store(Request $request, $lokasi)
    {
        // 1. BERSIHKAN FORMAT RUPIAH & RIBUAN
        $inputs = $request->all();
        $currencyFields = ['nilai_perolehan', 'harga_satuan', 'jumlah'];

        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        // 2. VALIDASI
        $validator = Validator::make($request->all(), [
            'kode_barang'        => 'required|string|max:100|unique:tanahs,kode_barang',
            'nama_barang'        => 'required|string|max:255',
            'nibar'              => 'nullable|string|max:255',
            'nomor_register'     => 'nullable|string|max:255',
            'spesifikasi_barang' => 'nullable|string',
            'spesifikasi_lainnya'=> 'nullable|string',
            'jumlah'             => 'required|numeric', 
            'satuan'             => 'required|string|max:255',
            'Lok'                => 'required|string', 
            'titik_koordinat'    => 'nullable|string|max:255',
            'bukti_nama'         => 'nullable|string|max:255',
            'bukti_nomor'        => 'nullable|string|max:255',
            'bukti_tanggal'      => 'nullable|date',
            'nama_kepemilikan_dokumen' => 'nullable|string|max:255',
            'nilai_perolehan'    => 'required|numeric|min:0',
            'harga_satuan'       => 'nullable|numeric|min:0',
            'cara_perolehan'     => 'required|string|max:255',
            'tanggal_perolehan'  => 'required|date',
            'status_penggunaan'  => 'required|string|max:255',
            'keterangan'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $dataToStore = $validator->validated();
        $dataToStore['lokasi'] = $lokasi;
        
        $tanah = Tanah::create($dataToStore);

        // 3. NOTIFIKASI
        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'ditambahkan', 'Tanah', $tanah->nama_barang
            ));
        }

        return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Tanah berhasil ditambahkan.');
    }

    public function edit($lokasi, Tanah $tanah)
    {
        if ($tanah->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.tanah.edit", compact('tanah', 'lokasi'));
    }

    /**
     * Update Data Tanah
     */
    public function update(Request $request, $lokasi, Tanah $tanah)
    {
        if ($tanah->lokasi !== $lokasi) abort(404);

        $inputs = $request->all();
        $currencyFields = ['nilai_perolehan', 'harga_satuan', 'jumlah'];
        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        $validator = Validator::make($request->all(), [
            'kode_barang'        => "required|string|max:100|unique:tanahs,kode_barang,{$tanah->kode_barang},kode_barang",
            'nama_barang'        => 'required|string|max:255',
            'nibar'              => 'nullable|string|max:255',
            'nomor_register'     => 'nullable|string|max:255',
            'spesifikasi_barang' => 'nullable|string',
            'spesifikasi_lainnya'=> 'nullable|string',
            'jumlah'             => 'required|numeric',
            'satuan'             => 'required|string|max:255',
            'Lok'                => 'required|string', 
            'titik_koordinat'    => 'nullable|string|max:255',
            'bukti_nama'         => 'nullable|string|max:255',
            'bukti_nomor'        => 'nullable|string|max:255',
            'bukti_tanggal'      => 'nullable|date',
            'nama_kepemilikan_dokumen' => 'nullable|string|max:255',
            'nilai_perolehan'    => 'required|numeric|min:0',
            'harga_satuan'       => 'nullable|numeric|min:0',
            'cara_perolehan'     => 'required|string|max:255',
            'tanggal_perolehan'  => 'required|date',
            'status_penggunaan'  => 'required|string|max:255',
            'keterangan'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $tanah->update($validator->validated());

        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'diperbarui', 'Tanah', $tanah->nama_barang
            ));
        }

        return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Tanah berhasil diperbarui.');
    }

    public function destroy($lokasi, Tanah $tanah)
    {
        if ($tanah->lokasi !== $lokasi) abort(404);
        
        $itemName = $tanah->nama_barang;
        $tanah->delete();

        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'dihapus', 'Tanah', $itemName
            ));
        }

        return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Tanah berhasil dihapus.');
    }

    public function print($lokasi)
    {
        $dataTanah = Tanah::where('lokasi', $lokasi)->latest('updated_at')->get();
        return view("pages.{$lokasi}.tanah.print", compact('dataTanah', 'lokasi'));
    }
}