<?php

namespace App\Http\Controllers;

use App\Models\Jalan;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class JalanController extends Controller
{
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        $query = Jalan::where('lokasi', $lokasi);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('jalan_nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('jalan_kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('jalan_nomor_register', 'LIKE', "%{$search}%")
                  ->orWhere('jalan_lokasi_fisik', 'LIKE', "%{$search}%");
            });
        }
        
        $dataJalan = $query->latest('updated_at')->paginate(10);
        return view("pages.{$lokasi}.jalan.index", compact('dataJalan', 'lokasi', 'search'));
    }

    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        $results = Jalan::where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('jalan_nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('jalan_kode_barang', 'LIKE', "%{$search}%");
            })
            ->limit(5)
            ->get(['jalan_nama_barang as label', 'jalan_kode_barang as value']);

        return response()->json($results);
    }

    public function create($lokasi)
    {
        return view("pages.{$lokasi}.jalan.create", compact('lokasi'));
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
            'kode_barang'                       => 'required|string|max:100|unique:jalans,jalan_kode_barang',
            'nama_barang'                       => 'required|string|max:255',
            'nibar'                             => 'nullable|string|max:255',
            'nomor_register'                    => 'required|string|max:255',
            'spesifikasi_barang'                => 'nullable|string',
            'spesifikasi_lainnya'               => 'nullable|string',
            'nomor_ruas_jalan_jembatan_irigasi' => 'nullable|string|max:255',
            'Lok'                               => 'required|string', 
            'titik_koordinat'                   => 'nullable|string|max:255',
            'status_kepemilikan_tanah'          => 'nullable|string|max:255',
            'jumlah'                            => 'required|numeric|min:0',
            'satuan'                            => 'required|string|max:255',
            'harga_satuan'                      => 'required|numeric|min:0',
            'nilai_perolehan'                   => 'required|numeric|min:0',
            'cara_perolehan'                    => 'required|string|max:255',
            'tanggal_perolehan'                 => 'required|date',
            'status_penggunaan'                 => 'nullable|string|max:255',
            'keterangan'                        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $jalan = Jalan::create([
            'jalan_kode_barang'                       => $request->kode_barang,
            'jalan_nama_barang'                       => $request->nama_barang,
            'jalan_nibar'                             => $request->nibar,
            'jalan_nomor_register'                    => $request->nomor_register,
            'jalan_spesifikasi_barang'                => $request->spesifikasi_barang,
            'jalan_spesifikasi_lainnya'               => $request->spesifikasi_lainnya,
            'jalan_nomor_ruas_jalan_jembatan_irigasi' => $request->nomor_ruas_jalan_jembatan_irigasi,
            'jalan_lokasi_fisik'                      => $request->Lok,
            'jalan_titik_koordinat'                   => $request->titik_koordinat,
            'jalan_status_kepemilikan_tanah'          => $request->status_kepemilikan_tanah,
            'jalan_jumlah'                            => $request->jumlah,
            'jalan_satuan'                            => $request->satuan,
            'jalan_harga_satuan'                      => $request->harga_satuan,
            'jalan_nilai_perolehan'                   => $request->nilai_perolehan,
            'jalan_cara_perolehan'                    => $request->cara_perolehan,
            'jalan_tanggal_perolehan'                 => $request->tanggal_perolehan,
            'jalan_status_penggunaan'                 => $request->status_penggunaan,
            'jalan_keterangan'                        => $request->keterangan,
            'lokasi'                                  => $lokasi,
        ]);

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'ditambahkan', 'Jalan, Irigasi & Jaringan', $jalan->jalan_nama_barang));

        return redirect()->route('lokasi.jalan.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($lokasi, $kode_barang)
    {
        $jalan = Jalan::findOrFail($kode_barang);
        if ($jalan->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.jalan.edit", compact('jalan', 'lokasi'));
    }

    public function update(Request $request, $lokasi, $kode_barang)
    {
        $jalan = Jalan::findOrFail($kode_barang);
        if ($jalan->lokasi !== $lokasi) abort(404);

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
            'kode_barang'                       => "required|string|max:100|unique:jalans,jalan_kode_barang,{$jalan->jalan_kode_barang},jalan_kode_barang",
            'nama_barang'                       => 'required|string|max:255',
            'nibar'                             => 'nullable|string|max:255',
            'nomor_register'                    => 'required|string|max:255',
            'spesifikasi_barang'                => 'nullable|string',
            'spesifikasi_lainnya'               => 'nullable|string',
            'nomor_ruas_jalan_jembatan_irigasi' => 'nullable|string|max:255',
            'Lok'                               => 'required|string', 
            'titik_koordinat'                   => 'nullable|string|max:255',
            'status_kepemilikan_tanah'          => 'nullable|string|max:255',
            'jumlah'                            => 'required|numeric|min:0',
            'satuan'                            => 'required|string|max:255',
            'harga_satuan'                      => 'required|numeric|min:0',
            'nilai_perolehan'                   => 'required|numeric|min:0',
            'cara_perolehan'                    => 'required|string|max:255',
            'tanggal_perolehan'                 => 'required|date',
            'status_penggunaan'                 => 'nullable|string|max:255',
            'keterangan'                        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $jalan->update([
            'jalan_kode_barang'                       => $request->kode_barang,
            'jalan_nama_barang'                       => $request->nama_barang,
            'jalan_nibar'                             => $request->nibar,
            'jalan_nomor_register'                    => $request->nomor_register,
            'jalan_spesifikasi_barang'                => $request->spesifikasi_barang,
            'jalan_spesifikasi_lainnya'               => $request->spesifikasi_lainnya,
            'jalan_nomor_ruas_jalan_jembatan_irigasi' => $request->nomor_ruas_jalan_jembatan_irigasi,
            'jalan_lokasi_fisik'                      => $request->Lok,
            'jalan_titik_koordinat'                   => $request->titik_koordinat,
            'jalan_status_kepemilikan_tanah'          => $request->status_kepemilikan_tanah,
            'jalan_jumlah'                            => $request->jumlah,
            'jalan_satuan'                            => $request->satuan,
            'jalan_harga_satuan'                      => $request->harga_satuan,
            'jalan_nilai_perolehan'                   => $request->nilai_perolehan,
            'jalan_cara_perolehan'                    => $request->cara_perolehan,
            'jalan_tanggal_perolehan'                 => $request->tanggal_perolehan,
            'jalan_status_penggunaan'                 => $request->status_penggunaan,
            'jalan_keterangan'                        => $request->keterangan,
        ]);

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'diperbarui', 'Jalan, Irigasi & Jaringan', $jalan->jalan_nama_barang));

        return redirect()->route('lokasi.jalan.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($lokasi, $kode_barang)
    {
        $jalan = Jalan::findOrFail($kode_barang);
        if ($jalan->lokasi !== $lokasi) abort(404);
        $itemName = $jalan->jalan_nama_barang;
        $jalan->delete();

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'dihapus', 'Jalan, Irigasi & Jaringan', $itemName));

        return redirect()->route('lokasi.jalan.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil dihapus.');
    }

    public function print($lokasi)
    {
        $dataJalan = Jalan::where('lokasi', $lokasi)->latest('updated_at')->get();
        return view("pages.{$lokasi}.jalan.print", compact('dataJalan', 'lokasi'));
    }
}