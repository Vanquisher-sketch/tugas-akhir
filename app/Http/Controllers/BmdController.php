<?php

namespace App\Http\Controllers;

use App\Models\Bmd;
use App\Models\Peralatan;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class BmdController extends Controller
{
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');

        $bmds = Bmd::with('peralatan')
            ->where('lokasi', $lokasi)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pemakai_nama', 'LIKE', "%{$search}%")
                      ->orWhere('bast_nomor', 'LIKE', "%{$search}%")
                      ->orWhereHas('peralatan', function ($subQ) use ($search) {
                          $subQ->where('nama_barang', 'LIKE', "%{$search}%")
                               ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                               ->orWhere('nomor_polisi', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view("pages.{$lokasi}.bmd.index", compact('bmds', 'lokasi', 'search'));
    }

    /**
     * Fitur Saran Pencarian (Autocomplete)
     */
    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        $results = Bmd::with('peralatan')
            ->where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('pemakai_nama', 'LIKE', "%{$search}%")
                  ->orWhereHas('peralatan', function($subQ) use ($search) {
                      $subQ->where('nama_barang', 'LIKE', "%{$search}%");
                  });
            })
            ->limit(5)
            ->get();

        $formatted = $results->map(function($item) {
            return [
                'label' => $item->pemakai_nama . " (" . ($item->peralatan->nama_barang ?? 'Aset') . ")",
                'value' => $item->pemakai_nama
            ];
        });

        return response()->json($formatted);
    }

    public function create($lokasi)
    {
        // REVISI: Ambil daftar kode_barang yang sudah ada di tabel bmds (sudah terpakai)
        $sudahTerpakai = Bmd::where('lokasi', $lokasi)->pluck('peralatan_kode')->toArray();

        // Ambil peralatan yang BELUM terpakai (NotIn)
        $peralatans = Peralatan::where('lokasi', $lokasi)
            ->whereNotIn('kode_barang', $sudahTerpakai)
            ->select('kode_barang', 'nama_barang', 'nomor_polisi')
            ->get();

        return view("pages.{$lokasi}.bmd.create", compact('lokasi', 'peralatans'));
    }

    public function store(Request $request, $lokasi)
    {
        $validated = $this->validateRequest($request);
        
        DB::beginTransaction();
        try {
            $dataToStore = $validated;
            $dataToStore['lokasi'] = $lokasi;

            if ($request->hasFile('bast_file')) {
                $dataToStore['bast_file'] = $request->file('bast_file')->store('uploads/bast', 'public');
            }

            $bmd = Bmd::create($dataToStore);
            DB::commit();

            $this->sendNotification($bmd, 'ditambahkan');
            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function edit($lokasi, Bmd $bmd)
    {
        if ($bmd->lokasi !== $lokasi) abort(404);

        // REVISI: Ambil barang yang dipakai orang lain (kecuali data yang sedang diedit)
        $dipakaiOrangLain = Bmd::where('lokasi', $lokasi)
            ->where('id', '!=', $bmd->id)
            ->pluck('peralatan_kode')
            ->toArray();

        // Tampilkan barang yang tersedia + barang milik BAST ini sendiri
        $peralatans = Peralatan::where('lokasi', $lokasi)
            ->whereNotIn('kode_barang', $dipakaiOrangLain)
            ->select('kode_barang', 'nama_barang', 'nomor_polisi')
            ->get();

        return view("pages.{$lokasi}.bmd.edit", compact('bmd', 'lokasi', 'peralatans'));
    }

    public function update(Request $request, $lokasi, Bmd $bmd)
    {
        if ($bmd->lokasi !== $lokasi) abort(404);
        $validated = $this->validateRequest($request);

        DB::beginTransaction();
        try {
            $dataToUpdate = $validated;
            if ($request->hasFile('bast_file')) {
                if ($bmd->bast_file) Storage::disk('public')->delete($bmd->bast_file);
                $dataToUpdate['bast_file'] = $request->file('bast_file')->store('uploads/bast', 'public');
            }
            $bmd->update($dataToUpdate);
            DB::commit();
            $this->sendNotification($bmd, 'diperbarui');
            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function destroy($lokasi, Bmd $bmd)
    {
        if ($bmd->lokasi !== $lokasi) abort(404);
        $bmdDataForNotif = clone $bmd;
        if ($bmd->bast_file) Storage::disk('public')->delete($bmd->bast_file);
        $bmd->delete();
        $this->sendNotification($bmdDataForNotif, 'dihapus', true);
        return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])->with('success', 'Data berhasil dihapus.');
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'peralatan_kode'      => 'required|exists:peralatans,kode_barang',
            'alamat_penggunaan'   => 'required|string',
            'pemakai_nama'        => 'required|string',
            'pemakai_status'      => 'required|string',
            'pemakai_identitas'   => 'required|string',
            'bast_nomor'          => 'nullable|string',
            'bast_tanggal'        => 'nullable|date',
            'bast_file'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            // Pastikan field pajak masuk jika ada di form create/edit
            'tanggal_pajak'       => 'nullable|date',
            'tanggal_stnk'        => 'nullable|date',
        ]);
    }

    private function sendNotification($bmd, $action, $isDelete = false)
    {
        $recipients = User::whereIn('role_id', [1, 2])->get();
        $namaBarang = $bmd->peralatan->nama_barang ?? 'Aset';
        $pesan = $isDelete ? "Data pemakaian {$bmd->pemakai_nama} dihapus" : "Aset {$namaBarang} oleh {$bmd->pemakai_nama}";
        Notification::send($recipients, new DataModificationNotification(Auth::user(), $action, 'Penggunaan BMD', $pesan));
    }
}