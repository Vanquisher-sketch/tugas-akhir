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

    /**
     * Fitur Saran Pencarian (Autocomplete)
     */
    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        $results = Rusak::where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('no_id_pemda', 'LIKE', "%{$search}%");
            })
            ->limit(5)
            ->get(['nama_barang as label', 'no_id_pemda as value']);

        return response()->json($results);
    }

    public function create($lokasi)
    {
        return view("pages.{$lokasi}.rusak.create", compact('lokasi'));
    }

    public function store(Request $request, $lokasi)
    {
        $inputs = $request->all();
        if (isset($inputs['harga_perolehan'])) {
            $cleanValue = str_replace('.', '', $inputs['harga_perolehan']);
            $inputs['harga_perolehan'] = str_replace(',', '.', $cleanValue);
        }
        $request->replace($inputs);

        $validator = Validator::make($request->all(), [
            'no_id_pemda'     => 'required|string|max:100|unique:rusaks,no_id_pemda',
            'nama_barang'     => 'required|string|max:255',
            'tahun_perolehan' => 'required|digits:4',
            'harga_perolehan' => 'required|numeric|min:0',
            'kondisi'         => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $dataToStore = $validator->validated();
        $dataToStore['lokasi'] = $lokasi;
        $rusak = Rusak::create($dataToStore);

        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'ditambahkan', 'Barang Rusak', $rusak->nama_barang));

        return redirect()->route('lokasi.rusak.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($lokasi, Rusak $rusak)
    {
        if ($rusak->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.rusak.edit", compact('rusak', 'lokasi'));
    }

    public function update(Request $request, $lokasi, Rusak $rusak)
    {
        if ($rusak->lokasi !== $lokasi) abort(404);

        $inputs = $request->all();
        if (isset($inputs['harga_perolehan'])) {
            $cleanValue = str_replace('.', '', $inputs['harga_perolehan']);
            $inputs['harga_perolehan'] = str_replace(',', '.', $cleanValue);
        }
        $request->replace($inputs);

        $validator = Validator::make($request->all(), [
            'no_id_pemda'     => "required|string|max:100|unique:rusaks,no_id_pemda,{$rusak->no_id_pemda},no_id_pemda",
            'nama_barang'     => 'required|string|max:255',
            'harga_perolehan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $rusak->update($validator->validated());

        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'diperbarui', 'Barang Rusak', $rusak->nama_barang));

        return redirect()->route('lokasi.rusak.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($lokasi, Rusak $rusak)
    {
        if ($rusak->lokasi !== $lokasi) abort(404);
        $itemName = $rusak->nama_barang;
        $rusak->delete();

        $recipients = User::whereIn('role_id', [1, 2])->get();
        Notification::send($recipients, new DataModificationNotification(Auth::user(), 'dihapus', 'Barang Rusak', $itemName));

        return redirect()->route('lokasi.rusak.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil dihapus.');
    }

    public function print($lokasi)
    {
        $dataRusak = Rusak::where('lokasi', $lokasi)->latest('updated_at')->get();
        return view("pages.{$lokasi}.rusak.print", compact('dataRusak', 'lokasi'));
    }
}