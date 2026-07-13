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
            // 🌟 REVISI: Sesuaikan pencarian dengan nama kolom berawalan 'tanah_'
            $query->where(function($q) use ($search) {
                $q->where('tanah_nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('tanah_lokasi_fisik', 'LIKE', "%{$search}%") // Perubahan dari 'Lok'
                  ->orWhere('tanah_kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('tanah_bukti_nomor', 'LIKE', "%{$search}%");
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
        
        $results = Tanah::where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('tanah_nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('tanah_kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('tanah_lokasi_fisik', 'LIKE', "%{$search}%");
            })
            ->limit(5)
            // 🌟 REVISI: Alias pencarian diubah sesuai kolom database baru
            ->get(['tanah_nama_barang as label', 'tanah_kode_barang as value']);

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
        // 1. BERSIHKAN FORMAT RUPIAH & RIBUAN (Sesuai dengan nama input dari form)
        $inputs = $request->all();
        $currencyFields = ['nilai_perolehan', 'harga_satuan', 'jumlah'];

        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        // 2. VALIDASI (Pengecekan unik diarahkan ke tanah_kode_barang)
        $validator = Validator::make($request->all(), [
            'kode_barang'        => 'required|string|max:100|unique:tanahs,tanah_kode_barang',
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
        
        // 3. MAPPING KE DATABASE DENGAN PREFIX 'tanah_'
        $tanah = Tanah::create([
            'tanah_kode_barang'        => $request->kode_barang,
            'tanah_nama_barang'        => $request->nama_barang,
            'tanah_nibar'              => $request->nibar,
            'tanah_nomor_register'     => $request->nomor_register,
            'tanah_spesifikasi_barang' => $request->spesifikasi_barang,
            'tanah_spesifikasi_lainnya'=> $request->spesifikasi_lainnya,
            'tanah_jumlah'             => $request->jumlah,
            'tanah_satuan'             => $request->satuan,
            'tanah_lokasi_fisik'       => $request->Lok,
            'tanah_titik_koordinat'    => $request->titik_koordinat,
            'tanah_bukti_nama'         => $request->bukti_nama,
            'tanah_bukti_nomor'        => $request->bukti_nomor,
            'tanah_bukti_tanggal'      => $request->bukti_tanggal,
            'tanah_nama_kepemilikan_dokumen' => $request->nama_kepemilikan_dokumen,
            'tanah_nilai_perolehan'    => $request->nilai_perolehan,
            'tanah_harga_satuan'       => $request->harga_satuan,
            'tanah_cara_perolehan'     => $request->cara_perolehan,
            'tanah_tanggal_perolehan'  => $request->tanggal_perolehan,
            'tanah_status_penggunaan'  => $request->status_penggunaan,
            'tanah_keterangan'         => $request->keterangan,
            'lokasi'                   => $lokasi,
        ]);

        // 4. NOTIFIKASI
        $recipients = User::whereIn('user_role_id', [1, 2])->get(); // REVISI: Kolom role
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'ditambahkan', 'Tanah', $tanah->tanah_nama_barang
            ));
        }

        return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Tanah berhasil ditambahkan.');
    }

    // 🌟 REVISI: Gunakan string eksplisit $kode_barang untuk pencarian Primary Key
    public function edit($lokasi, $kode_barang)
    {
        $tanah = Tanah::findOrFail($kode_barang);
        if ($tanah->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.tanah.edit", compact('tanah', 'lokasi'));
    }

    /**
     * Update Data Tanah
     */
    public function update(Request $request, $lokasi, $kode_barang)
    {
        $tanah = Tanah::findOrFail($kode_barang);
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
            // Pengecualian unik disesuaikan secara eksplisit ke kolom database
            'kode_barang'        => "required|string|max:100|unique:tanahs,tanah_kode_barang,{$tanah->tanah_kode_barang},tanah_kode_barang",
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
        
        // Eksekusi Update menggunakan Mapping
        $tanah->update([
            'tanah_kode_barang'        => $request->kode_barang,
            'tanah_nama_barang'        => $request->nama_barang,
            'tanah_nibar'              => $request->nibar,
            'tanah_nomor_register'     => $request->nomor_register,
            'tanah_spesifikasi_barang' => $request->spesifikasi_barang,
            'tanah_spesifikasi_lainnya'=> $request->spesifikasi_lainnya,
            'tanah_jumlah'             => $request->jumlah,
            'tanah_satuan'             => $request->satuan,
            'tanah_lokasi_fisik'       => $request->Lok,
            'tanah_titik_koordinat'    => $request->titik_koordinat,
            'tanah_bukti_nama'         => $request->bukti_nama,
            'tanah_bukti_nomor'        => $request->bukti_nomor,
            'tanah_bukti_tanggal'      => $request->bukti_tanggal,
            'tanah_nama_kepemilikan_dokumen' => $request->nama_kepemilikan_dokumen,
            'tanah_nilai_perolehan'    => $request->nilai_perolehan,
            'tanah_harga_satuan'       => $request->harga_satuan,
            'tanah_cara_perolehan'     => $request->cara_perolehan,
            'tanah_tanggal_perolehan'  => $request->tanggal_perolehan,
            'tanah_status_penggunaan'  => $request->status_penggunaan,
            'tanah_keterangan'         => $request->keterangan,
        ]);

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'diperbarui', 'Tanah', $tanah->tanah_nama_barang
            ));
        }

        return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Tanah berhasil diperbarui.');
    }

    public function destroy($lokasi, $kode_barang)
    {
        $tanah = Tanah::findOrFail($kode_barang);
        if ($tanah->lokasi !== $lokasi) abort(404);
        
        $itemName = $tanah->tanah_nama_barang;
        $tanah->delete();

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
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