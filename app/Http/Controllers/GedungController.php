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
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        $query = Gedung::where('lokasi', $lokasi);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('gedung_nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('gedung_kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('gedung_lokasi_fisik', 'LIKE', "%{$search}%");
            });
        }
        
        $dataGedung = $query->latest('updated_at')->paginate(10);
        return view("pages.{$lokasi}.gedung.index", compact('dataGedung', 'lokasi', 'search'));
    }

    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        $results = Gedung::where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('gedung_nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('gedung_kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('gedung_lokasi_fisik', 'LIKE', "%{$search}%");
            })
            ->limit(5)
            ->get(['gedung_nama_barang as label', 'gedung_kode_barang as value']);

        return response()->json($results);
    }

    public function create($lokasi)
    {
        return view("pages.{$lokasi}.gedung.create", compact('lokasi'));
    }

    public function store(Request $request, $lokasi)
    {
        $inputs = $request->all();
        $currencyFields = ['harga_satuan', 'nilai_perolehan', 'jumlah'];

        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        $validator = Validator::make($request->all(), [
            'kode_barang'               => 'required|string|max:100|unique:gedungs,gedung_kode_barang',
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
        
        $gedung = Gedung::create([
            'gedung_kode_barang'               => $request->kode_barang,
            'gedung_nama_barang'               => $request->nama_barang,
            'gedung_nibar'                     => $request->nbar,
            'gedung_nomor_register'            => $request->nomor_register,
            'gedung_spesifikasi_barang'        => $request->spesifikasi_barang,
            'gedung_spesifikasi_lainnya'       => $request->spesifikasi_lainnya,
            'gedung_jumlah_lantai'             => $request->jumlah_lantai,
            'gedung_lokasi_fisik'              => $request->Lok,
            'gedung_titik_koordinat'           => $request->titik_koordinat,
            'gedung_status_kepemilikan_tanah'  => $request->status_kepemilikan_tanah,
            'gedung_jumlah'                    => $request->jumlah,
            'gedung_satuan'                    => $request->satuan,
            'gedung_harga_satuan'              => $request->harga_satuan,
            'gedung_nilai_perolehan'           => $request->nilai_perolehan,
            'gedung_cara_perolehan'            => $request->cara_perolehan,
            'gedung_tanggal_perolehan'         => $request->tanggal_perolehan,
            'gedung_status_penggunaan'         => $request->status_penggunaan,
            'gedung_keterangan'                => $request->keterangan,
            'lokasi'                           => $lokasi,
        ]);

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'ditambahkan', 'Gedung & Bangunan', $gedung->gedung_nama_barang
            ));
        }

        return redirect()->route('lokasi.gedung.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Gedung & Bangunan berhasil ditambahkan.');
    }

    public function edit($lokasi, $kode_barang)
    {
        $gedung = Gedung::findOrFail($kode_barang);
        if ($gedung->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.gedung.edit", compact('gedung', 'lokasi'));
    }

    public function update(Request $request, $lokasi, $kode_barang)
    {
        $gedung = Gedung::findOrFail($kode_barang);
        if ($gedung->lokasi !== $lokasi) abort(404);

        $inputs = $request->all();
        $currencyFields = ['harga_satuan', 'nilai_perolehan', 'jumlah'];
        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        $validator = Validator::make($request->all(), [
            'kode_barang'               => "required|string|max:100|unique:gedungs,gedung_kode_barang,{$gedung->gedung_kode_barang},gedung_kode_barang",
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
        
        $gedung->update([
            'gedung_kode_barang'               => $request->kode_barang,
            'gedung_nama_barang'               => $request->nama_barang,
            'gedung_nibar'                     => $request->nbar,
            'gedung_nomor_register'            => $request->nomor_register,
            'gedung_spesifikasi_barang'        => $request->spesifikasi_barang,
            'gedung_spesifikasi_lainnya'       => $request->spesifikasi_lainnya,
            'gedung_jumlah_lantai'             => $request->jumlah_lantai,
            'gedung_lokasi_fisik'              => $request->Lok,
            'gedung_titik_koordinat'           => $request->titik_koordinat,
            'gedung_status_kepemilikan_tanah'  => $request->status_kepemilikan_tanah,
            'gedung_jumlah'                    => $request->jumlah,
            'gedung_satuan'                    => $request->satuan,
            'gedung_harga_satuan'              => $request->harga_satuan,
            'gedung_nilai_perolehan'           => $request->nilai_perolehan,
            'gedung_cara_perolehan'            => $request->cara_perolehan,
            'gedung_tanggal_perolehan'         => $request->tanggal_perolehan,
            'gedung_status_penggunaan'         => $request->status_penggunaan,
            'gedung_keterangan'                => $request->keterangan,
        ]);

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'diperbarui', 'Gedung & Bangunan', $gedung->gedung_nama_barang
            ));
        }

        return redirect()->route('lokasi.gedung.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Gedung & Bangunan berhasil diperbarui.');
    }

    public function destroy($lokasi, $kode_barang)
    {
        $gedung = Gedung::findOrFail($kode_barang);
        if ($gedung->lokasi !== $lokasi) abort(404);
        
        $itemName = $gedung->gedung_nama_barang;
        $gedung->delete();
        
        $recipients = User::whereIn('user_role_id', [1, 2])->get();
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