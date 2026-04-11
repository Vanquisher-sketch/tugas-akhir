<?php

namespace App\Http\Controllers;

use App\Models\Rusak;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class RusakController extends Controller
{
    /**
     * Menampilkan daftar data barang rusak berdasarkan lokasi dan pencarian.
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        $query = Rusak::where('lokasi', $lokasi);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('no_id_pemda', 'LIKE', "%{$search}%")
                  ->orWhere('spesifikasi', 'LIKE', "%{$search}%")
                  ->orWhere('no_polisi', 'LIKE', "%{$search}%");
            });
        }
        
        $dataRusak = $query->latest('updated_at')->paginate(10);
        
        return view("pages.{$lokasi}.rusak.index", compact('dataRusak', 'lokasi', 'search'));
    }

    public function create($lokasi)
    {
        return view("pages.{$lokasi}.rusak.create", compact('lokasi'));
    }

    /**
     * Menyimpan data baru (STORE).
     */
    public function store(Request $request, $lokasi)
    {
        // 1. BERSIHKAN FORMAT RUPIAH
        $inputs = $request->all();
        if (isset($inputs['harga_perolehan'])) {
            $cleanValue = str_replace('.', '', $inputs['harga_perolehan']);
            $inputs['harga_perolehan'] = str_replace(',', '.', $cleanValue);
        }
        $request->replace($inputs);

        // 2. VALIDASI (no_id_pemda harus Unique)
        $validator = Validator::make($request->all(), [
            'no_id_pemda'     => 'required|string|max:100|unique:rusaks,no_id_pemda',
            'nama_barang'     => 'required|string|max:255',
            'spesifikasi'     => 'nullable|string|max:255',
            'no_polisi'       => 'nullable|string|max:255',
            'tahun_perolehan' => 'required|digits:4',
            'harga_perolehan' => 'required|numeric|min:0',
            'kondisi'         => 'required|string|max:255',
            'tercatat_di_kib' => 'nullable|string|max:255',
            'keterangan'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $dataToStore = $validator->validated();
        $dataToStore['lokasi'] = $lokasi;
        
        $rusak = Rusak::create($dataToStore);

        // 3. NOTIFIKASI
        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'ditambahkan', 'Barang Rusak', $rusak->nama_barang
            ));
        }

        return redirect()->route('lokasi.rusak.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Barang Rusak berhasil ditambahkan.');
    }

    public function edit($lokasi, Rusak $rusak)
    {
        // Laravel otomatis mencari $rusak berdasarkan no_id_pemda
        if ($rusak->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.rusak.edit", compact('rusak', 'lokasi'));
    }

    /**
     * Memperbarui data (UPDATE).
     */
    public function update(Request $request, $lokasi, Rusak $rusak)
    {
        if ($rusak->lokasi !== $lokasi) abort(404);

        // 1. BERSIHKAN FORMAT
        $inputs = $request->all();
        if (isset($inputs['harga_perolehan'])) {
            $cleanValue = str_replace('.', '', $inputs['harga_perolehan']);
            $inputs['harga_perolehan'] = str_replace(',', '.', $cleanValue);
        }
        $request->replace($inputs);

        // 2. VALIDASI (Kecualikan no_id_pemda milik sendiri)
        $validator = Validator::make($request->all(), [
            'no_id_pemda'     => "required|string|max:100|unique:rusaks,no_id_pemda,{$rusak->no_id_pemda},no_id_pemda",
            'nama_barang'     => 'required|string|max:255',
            'spesifikasi'     => 'nullable|string|max:255',
            'no_polisi'       => 'nullable|string|max:255',
            'tahun_perolehan' => 'required|digits:4',
            'harga_perolehan' => 'required|numeric|min:0',
            'kondisi'         => 'required|string|max:255',
            'tercatat_di_kib' => 'nullable|string|max:255',
            'keterangan'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $rusak->update($validator->validated());

        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'diperbarui', 'Barang Rusak', $rusak->nama_barang
            ));
        }

        return redirect()->route('lokasi.rusak.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Barang Rusak berhasil diperbarui.');
    }

    public function destroy($lokasi, Rusak $rusak)
    {
        if ($rusak->lokasi !== $lokasi) abort(404);
        
        $itemName = $rusak->nama_barang;
        $rusak->delete();

        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'dihapus', 'Barang Rusak', $itemName
            ));
        }

        return redirect()->route('lokasi.rusak.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Barang Rusak berhasil dihapus.');
    }

    public function print($lokasi)
    {
        $dataRusak = Rusak::where('lokasi', $lokasi)->latest('updated_at')->get();
        return view("pages.{$lokasi}.rusak.print", compact('dataRusak', 'lokasi'));
    }
}