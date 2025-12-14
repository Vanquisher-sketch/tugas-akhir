<?php

namespace App\Http\Controllers;

use App\Models\Tanah;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class TanahController extends Controller
{
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        $query = Tanah::where('lokasi', $lokasi);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('Lok', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%");
            });
        }
        
        $dataTanah = $query->latest()->paginate(10);
        return view("pages.{$lokasi}.tanah.index", compact('dataTanah', 'lokasi', 'search'));
    }

    public function create($lokasi)
    {
        return view("pages.{$lokasi}.tanah.create", compact('lokasi'));
    }

    public function store(Request $request, $lokasi)
    {
        // 1. BERSIHKAN FORMAT RUPIAH & ANGKA
        $inputs = $request->all();
        $currencyFields = ['nilai_perolehan', 'harga_satuan', 'jumlah'];

        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $inputs[$field] = preg_replace('/[^0-9.]/', '', $inputs[$field]);
            }
        }
        $request->replace($inputs);

        // 2. VALIDASI MANUAL
        $validator = Validator::make($request->all(), [
            'kode_barang'       => 'required|string|max:255',
            'nama_barang'       => 'required|string|max:255',
            'nibar'             => 'nullable|string|max:255',
            'nomor_register'    => 'nullable|string|max:255',
            'spesifikasi_barang'=> 'nullable|string',
            'spesifikasi_lainnya'=> 'nullable|string',
            'jumlah'            => 'required|numeric', 
            'satuan'            => 'required|string|max:255',
            
            // Sesuai Database (L Besar)
            'Lok'               => 'required|string', 
            
            'titik_koordinat'   => 'nullable|string|max:255',
            'bukti_nama'        => 'nullable|string|max:255',
            'bukti_nomor'       => 'nullable|string|max:255',
            'bukti_tanggal'     => 'nullable|date',
            'nama_kepemilikan_dokumen' => 'nullable|string|max:255',
            
            'nilai_perolehan'   => 'required|numeric|min:0',
            'harga_satuan'      => 'nullable|numeric|min:0',
            
            'cara_perolehan'    => 'required|string|max:255',
            'tanggal_perolehan' => 'required|date',
            'tanggal_penggunaan'=> 'nullable|date', // SUDAH DITAMBAHKAN
            'status_penggunaan' => 'required|string|max:255',
            'keterangan'        => 'nullable|string',
        ]);

        // JIKA VALIDASI GAGAL
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // 3. SET LOKASI SYSTEM
        $dataToStore = $validator->validated();
        $dataToStore['lokasi'] = $lokasi;
        
        // 4. SIMPAN
        $tanah = Tanah::create($dataToStore);

        // 5. NOTIFIKASI
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(
            Auth::user(), 'ditambahkan', 'Tanah', $tanah->nama_barang
        ));

        return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data tanah berhasil ditambahkan.');
    }

    public function edit($lokasi, Tanah $tanah)
    {
        if ($tanah->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.tanah.edit", compact('tanah', 'lokasi'));
    }

    public function update(Request $request, $lokasi, Tanah $tanah)
    {
        if ($tanah->lokasi !== $lokasi) abort(404);

        // 1. BERSIHKAN FORMAT
        $inputs = $request->all();
        $currencyFields = ['nilai_perolehan', 'harga_satuan', 'jumlah'];
        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $inputs[$field] = preg_replace('/[^0-9.]/', '', $inputs[$field]);
            }
        }
        $request->replace($inputs);

        // 2. VALIDASI MANUAL
        $validator = Validator::make($request->all(), [
            'kode_barang'       => 'required|string|max:255',
            'nama_barang'       => 'required|string|max:255',
            'nibar'             => 'nullable|string|max:255',
            'nomor_register'    => 'nullable|string|max:255',
            'spesifikasi_barang'=> 'nullable|string',
            'spesifikasi_lainnya'=> 'nullable|string',
            'jumlah'            => 'required|numeric',
            'satuan'            => 'required|string|max:255',
            'Lok'               => 'required|string', 
            'titik_koordinat'   => 'nullable|string|max:255',
            'bukti_nama'        => 'nullable|string|max:255',
            'bukti_nomor'       => 'nullable|string|max:255',
            'bukti_tanggal'     => 'nullable|date',
            'nama_kepemilikan_dokumen' => 'nullable|string|max:255',
            'nilai_perolehan'   => 'required|numeric|min:0',
            'harga_satuan'      => 'nullable|numeric|min:0',
            'cara_perolehan'    => 'required|string|max:255',
            'tanggal_perolehan' => 'required|date',
            'tanggal_penggunaan'=> 'nullable|date',
            'status_penggunaan' => 'required|string|max:255',
            'keterangan'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // 3. UPDATE
        $tanah->update($validator->validated());

        // 4. NOTIFIKASI
        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(
            Auth::user(), 'diperbarui', 'Tanah', $tanah->nama_barang
        ));

        return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data tanah berhasil diperbarui.');
    }

    public function destroy($lokasi, Tanah $tanah)
    {
        if ($tanah->lokasi !== $lokasi) abort(404);
        
        $itemName = $tanah->nama_barang;
        $tanah->delete();

        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(
            Auth::user(), 'dihapus', 'Tanah', $itemName
        ));

        return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data tanah berhasil dihapus.');
    }

    public function print($lokasi)
    {
        $dataTanah = Tanah::where('lokasi', $lokasi)->latest()->get();
        return view("pages.{$lokasi}.tanah.print", compact('dataTanah', 'lokasi'));
    }
}