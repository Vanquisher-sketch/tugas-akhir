<?php

namespace App\Http\Controllers;

use App\Models\Bmd;
use App\Models\Peralatan;
use App\Models\Pegawai;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BmdController extends Controller
{
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');

        $bmds = Bmd::with(['peralatan', 'pegawai', 'bendahara'])
            ->where('lokasi', $lokasi)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('bmd_bast_nomor', 'LIKE', "%{$search}%")
                      ->orWhereHas('pegawai', function ($subQ) use ($search) {
                          $subQ->where('pegawai_nama', 'LIKE', "%{$search}%"); 
                      })
                      ->orWhereHas('peralatan', function ($subQ) use ($search) {
                          $subQ->where('alat_nama_barang', 'LIKE', "%{$search}%") 
                               ->orWhere('alat_kode_barang', 'LIKE', "%{$search}%")
                               ->orWhere('alat_nomor_polisi', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view("pages.{$lokasi}.bmd.index", compact('bmds', 'lokasi', 'search'));
    }

    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        $results = Bmd::with(['peralatan', 'pegawai'])
            ->where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('bmd_bast_nomor', 'LIKE', "%{$search}%")
                  ->orWhereHas('pegawai', function($subQ) use ($search) {
                      $subQ->where('pegawai_nama', 'LIKE', "%{$search}%"); 
                  })
                  ->orWhereHas('peralatan', function($subQ) use ($search) {
                      $subQ->where('alat_nama_barang', 'LIKE', "%{$search}%"); 
                  });
            })
            ->limit(5)
            ->get();

        $formatted = $results->map(function($item) {
            $namaPemakai = $item->pegawai->pegawai_nama ?? 'Tidak Diketahui'; 
            $namaBarang = $item->peralatan->alat_nama_barang ?? 'Aset'; 
            return [
                'label' => $namaPemakai . " (" . $namaBarang . ")",
                'value' => $namaPemakai
            ];
        });

        return response()->json($formatted);
    }

    public function create($lokasi)
    {
        // 🌟 REVISI CERDAS: Tampilkan semua barang yang BELUM 'Aktif' (termasuk yang masih kosong/NULL)
        $peralatans = Peralatan::where('lokasi', $lokasi)
            ->where(function($query) {
                $query->where('alat_status_penggunaan', '!=', 'Aktif')
                      ->orWhereNull('alat_status_penggunaan')
                      ->orWhere('alat_status_penggunaan', '');
            })
            ->select('alat_kode_barang', 'alat_nama_barang', 'alat_nomor_polisi')
            ->get();

        $pegawais = Pegawai::where('lokasi', $lokasi)->orderBy('pegawai_nama')->get(); 

        return view("pages.{$lokasi}.bmd.create", compact('lokasi', 'peralatans', 'pegawais'));
    }

    public function store(Request $request, $lokasi)
    {
        $validated = $request->validate([
            'peralatan_kode'    => 'required|exists:peralatans,alat_kode_barang', 
            'pegawai_id'        => 'required|exists:pegawais,pegawai_nip', 
            'bendahara_id'      => 'required|exists:pegawais,pegawai_nip', 
            'pemakai_status'    => 'required|string',
            'pemakai_identitas' => 'required|string',
            'keterangan'        => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        try {
            $bastTanggal = date('Y-m-d'); 
            $totalData = Bmd::where('lokasi', $lokasi)->count() + 1;
            $nomorOtomatis = str_pad($totalData, 3, '0', STR_PAD_LEFT) . '/BAST/' . strtoupper($lokasi) . '/' . date('Y');

            $bmd = Bmd::create([
                'bmd_bast_nomor'        => $nomorOtomatis,
                'bmd_bast_tanggal'      => $bastTanggal,
                'bmd_alat_kode'         => $request->peralatan_kode,
                'bmd_pegawai_nip'       => $request->pegawai_id, 
                'bmd_bendahara_nip'     => $request->bendahara_id,
                'bmd_pemakai_status'    => $request->pemakai_status,
                'bmd_pemakai_identitas' => $request->pemakai_identitas,
                'bmd_keterangan'        => $request->keterangan,
                'lokasi'                => $lokasi
            ]);

            Peralatan::where('alat_kode_barang', $request->peralatan_kode)->update([
                'alat_status_penggunaan' => 'Aktif'
            ]);

            $peralatanData = Peralatan::where('alat_kode_barang', $request->peralatan_kode)->first();
            $pegawaiData = Pegawai::where('pegawai_nip', $request->pegawai_id)->first();
            $bendaharaData = Pegawai::where('pegawai_nip', $request->bendahara_id)->first();

            if (!Storage::disk('public')->exists('uploads/bast')) {
                Storage::disk('public')->makeDirectory('uploads/bast', 0755, true);
            }

            $pdf = Pdf::loadView("pages.{$lokasi}.bmd.cetak_bast", [
                'bast_nomor'        => $nomorOtomatis,
                'bast_tanggal'      => $bastTanggal,
                'lokasi'            => $lokasi,
                'keterangan'        => $request->keterangan,
                'pemakai_identitas' => $request->pemakai_identitas,
                'pemakai_status'    => $request->pemakai_status,
                'peralatan'         => $peralatanData,
                'pegawai'           => $pegawaiData,
                'bendahara'         => $bendaharaData
            ]);

            $namaFile = 'BAST-' . strtoupper($lokasi) . '-' . $bmd->bmd_id . '-' . time() . '.pdf';
            $pathPenyimpanan = 'uploads/bast/' . $namaFile;

            Storage::disk('public')->put($pathPenyimpanan, $pdf->output());

            $bmd->update([
                'bmd_bast_file' => $pathPenyimpanan
            ]);

            DB::commit();

            $this->sendNotification($bmd, 'ditambahkan');
            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])
                ->with('success', 'Data pemakaian berhasil disimpan. Surat ' . $nomorOtomatis . ' otomatis diterbitkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memproses otomatisasi BMD: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memproses data. Alasan: ' . $e->getMessage());
        }
    }

    public function edit($lokasi, $id) 
    {
        $bmd = Bmd::findOrFail($id);
        if ($bmd->lokasi !== $lokasi) abort(404);

        // 🌟 REVISI CERDAS: Tampilkan yang belum 'Aktif' ATAU barang yang memang sedang dipakai di transaksi ini
        $peralatans = Peralatan::where('lokasi', $lokasi)
            ->where(function($query) use ($bmd) {
                $query->where('alat_status_penggunaan', '!=', 'Aktif')
                      ->orWhereNull('alat_status_penggunaan')
                      ->orWhere('alat_status_penggunaan', '')
                      ->orWhere('alat_kode_barang', $bmd->bmd_alat_kode); 
            })
            ->select('alat_kode_barang', 'alat_nama_barang', 'alat_nomor_polisi')
            ->get();

        $pegawais = Pegawai::where('lokasi', $lokasi)->orderBy('pegawai_nama')->get();

        return view("pages.{$lokasi}.bmd.edit", compact('bmd', 'lokasi', 'peralatans', 'pegawais'));
    }

    public function update(Request $request, $lokasi, $id)
    {
        $bmd = Bmd::findOrFail($id);
        if ($bmd->lokasi !== $lokasi) abort(404);

        $request->validate([
            'peralatan_kode'    => 'required|exists:peralatans,alat_kode_barang',
            'pegawai_id'        => 'required|exists:pegawais,pegawai_nip',
            'bendahara_id'      => 'required|exists:pegawais,pegawai_nip',
            'pemakai_status'    => 'required|string',
            'pemakai_identitas' => 'required|string',
            'keterangan'        => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            if ($bmd->bmd_alat_kode !== $request->peralatan_kode) {
                Peralatan::where('alat_kode_barang', $bmd->bmd_alat_kode)->update(['alat_status_penggunaan' => 'Tidak Aktif']);
                Peralatan::where('alat_kode_barang', $request->peralatan_kode)->update(['alat_status_penggunaan' => 'Aktif']);
            }

            $bmd->update([
                'bmd_alat_kode'         => $request->peralatan_kode,
                'bmd_pegawai_nip'       => $request->pegawai_id, 
                'bmd_bendahara_nip'     => $request->bendahara_id,
                'bmd_pemakai_status'    => $request->pemakai_status,
                'bmd_pemakai_identitas' => $request->pemakai_identitas,
                'bmd_keterangan'        => $request->keterangan,
            ]);

            if ($bmd->bmd_bast_file && Storage::disk('public')->exists($bmd->bmd_bast_file)) {
                Storage::disk('public')->delete($bmd->bmd_bast_file);
            }

            $peralatanData = Peralatan::where('alat_kode_barang', $request->peralatan_kode)->first();
            $pegawaiData = Pegawai::where('pegawai_nip', $request->pegawai_id)->first();
            $bendaharaData = Pegawai::where('pegawai_nip', $request->bendahara_id)->first();

            $pdf = Pdf::loadView("pages.{$lokasi}.bmd.cetak_bast", [
                'bast_nomor'        => $bmd->bmd_bast_nomor,
                'bast_tanggal'      => $bmd->bmd_bast_tanggal,
                'lokasi'            => $lokasi,
                'keterangan'        => $request->keterangan,
                'pemakai_identitas' => $request->pemakai_identitas,
                'pemakai_status'    => $request->pemakai_status,
                'peralatan'         => $peralatanData,
                'pegawai'           => $pegawaiData,
                'bendahara'         => $bendaharaData
            ]);
            
            $namaFile = 'BAST-' . strtoupper($lokasi) . '-' . $bmd->bmd_id . '-' . time() . '.pdf';
            $pathPenyimpanan = 'uploads/bast/' . $namaFile;

            Storage::disk('public')->put($pathPenyimpanan, $pdf->output());
            $bmd->update(['bmd_bast_file' => $pathPenyimpanan]);

            DB::commit();
            $this->sendNotification($bmd, 'diperbarui');
            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])->with('success', 'Data dan dokumen BAST berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui BMD: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function destroy($lokasi, $id)
    {
        $bmd = Bmd::findOrFail($id);
        if ($bmd->lokasi !== $lokasi) abort(404);
        
        $bmdDataForNotif = clone $bmd;
        
        DB::beginTransaction();
        try {
            Peralatan::where('alat_kode_barang', $bmd->bmd_alat_kode)->update([
                'alat_status_penggunaan' => 'Tidak Aktif'
            ]);

            if ($bmd->bmd_bast_file && Storage::disk('public')->exists($bmd->bmd_bast_file)) {
                Storage::disk('public')->delete($bmd->bmd_bast_file);
            }
            
            $bmd->delete();
            DB::commit();

            $this->sendNotification($bmdDataForNotif, 'dihapus', true);
            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])->with('success', 'Data penggunaan dihapus, status barang kembali tersedia.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus BMD: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus data pemakaian.');
        }
    }

    public function bukaPdf($lokasi, $id)
    {
        $bmd = Bmd::findOrFail($id);

        if (!Storage::disk('public')->exists($bmd->bmd_bast_file)) {
            abort(404, 'File BAST tidak ditemukan di server.');
        }

        return response()->file(storage_path('app/public/' . $bmd->bmd_bast_file), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($bmd->bmd_bast_file).'"'
        ]);
    }

    private function sendNotification($bmd, $action, $isDelete = false)
    {
        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        
        $namaBarang = $bmd->peralatan->alat_nama_barang ?? 'Aset'; 
        $namaPemakai = $bmd->pegawai->pegawai_nama ?? 'Pegawai';
        
        $pesan = $isDelete 
            ? "Data pemakaian {$namaPemakai} dihapus" 
            : "Aset {$namaBarang} dialokasikan kepada {$namaPemakai}";
            
        Notification::send($recipients, new DataModificationNotification(Auth::user(), $action, 'Penggunaan BMD', $pesan));
    }
}