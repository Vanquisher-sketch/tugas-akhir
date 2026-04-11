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
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_register', 'LIKE', "%{$search}%")
                  ->orWhere('Lok', 'LIKE', "%{$search}%");
            });
        }
        
        $dataJalan = $query->latest('updated_at')->paginate(10);
        return view("pages.{$lokasi}.jalan.index", compact('dataJalan', 'lokasi', 'search'));
    }

    /**
     * Fitur Saran Pencarian (Autocomplete)
     */
    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        $results = Jalan::where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%");
            })
            ->limit(5)
            ->get(['nama_barang as label', 'kode_barang as value']);

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
            'kode_barang'                       => 'required|string|max:100|unique:jalans,kode_barang',
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
        
        $dataToStore = $validator->validated();
        $dataToStore['lokasi'] = $lokasi;
        $jalan = Jalan::create($dataToStore);

        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'ditambahkan', 'Jalan, Irigasi & Jaringan', $jalan->nama_barang));

        return redirect()->route('lokasi.jalan.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($lokasi, Jalan $jalan)
    {
        if ($jalan->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.jalan.edit", compact('jalan', 'lokasi'));
    }

    public function update(Request $request, $lokasi, Jalan $jalan)
    {
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
            'kode_barang'         => "required|string|max:100|unique:jalans,kode_barang,{$jalan->kode_barang},kode_barang",
            'nama_barang'         => 'required|string|max:255',
            'nomor_register'      => 'required|string|max:255',
            'Lok'                 => 'required|string', 
            'jumlah'              => 'required|numeric|min:0',
            'satuan'              => 'required|string|max:255',
            'harga_satuan'        => 'required|numeric|min:0',
            'nilai_perolehan'     => 'required|numeric|min:0',
            'cara_perolehan'      => 'required|string|max:255',
            'tanggal_perolehan'   => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $jalan->update($validator->validated());

        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'diperbarui', 'Jalan, Irigasi & Jaringan', $jalan->nama_barang));

        return redirect()->route('lokasi.jalan.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($lokasi, Jalan $jalan)
    {
        if ($jalan->lokasi !== $lokasi) abort(404);
        $itemName = $jalan->nama_barang;
        $jalan->delete();

        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'dihapus', 'Jalan, Irigasi & Jaringan', $itemName));

        return redirect()->route('lokasi.jalan.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil dihapus.');
    }

    public function print($lokasi)
    {
        $dataJalan = Jalan::where('lokasi', $lokasi)->latest('updated_at')->get();
        return view("pages.{$lokasi}.jalan.print", compact('dataJalan', 'lokasi'));
    }
}