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
    /**
     * Tampilkan Halaman Utama (Daftar Penggunaan BMD)
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');

        $bmds = Bmd::with(['peralatan', 'pegawai', 'bendahara'])
            ->where('lokasi', $lokasi)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('bmd_bast_nomor', 'LIKE', "%{$search}%")
                      ->orWhereHas('pegawai', function ($subQ) use ($search) {
                          $subQ->where('pegawai_nama', 'LIKE', "%{$search}%"); // 🌟 REVISI
                      })
                      ->orWhereHas('peralatan', function ($subQ) use ($search) {
                          $subQ->where('alat_nama_barang', 'LIKE', "%{$search}%") // 🌟 REVISI
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

    /**
     * Fitur Saran Pencarian (Autocomplete) via AJAX
     */
    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        $results = Bmd::with(['peralatan', 'pegawai'])
            ->where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('bmd_bast_nomor', 'LIKE', "%{$search}%")
                  ->orWhereHas('pegawai', function($subQ) use ($search) {
                      $subQ->where('pegawai_nama', 'LIKE', "%{$search}%"); // 🌟 REVISI
                  })
                  ->orWhereHas('peralatan', function($subQ) use ($search) {
                      $subQ->where('alat_nama_barang', 'LIKE', "%{$search}%"); // 🌟 REVISI
                  });
            })
            ->limit(5)
            ->get();

        $formatted = $results->map(function($item) {
            $namaPemakai = $item->pegawai->pegawai_nama ?? 'Tidak Diketahui'; // 🌟 REVISI
            $namaBarang = $item->peralatan->alat_nama_barang ?? 'Aset'; // 🌟 REVISI
            return [
                'label' => $namaPemakai . " (" . $namaBarang . ")",
                'value' => $namaPemakai
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Tampilkan Form Hubungkan Data (Tambah BMD)
     */
    public function create($lokasi)
    {
        // Menampilkan peralatan yang 'Tidak Aktif' 
        $peralatans = Peralatan::where('lokasi', $lokasi)
            ->where('alat_status_penggunaan', 'Tidak Aktif') // 🌟 REVISI
            ->select('alat_kode_barang', 'alat_nama_barang', 'alat_nomor_polisi')
            ->get();

        $pegawais = Pegawai::where('lokasi', $lokasi)->orderBy('pegawai_nama')->get(); // 🌟 REVISI

        return view("pages.{$lokasi}.bmd.create", compact('lokasi', 'peralatans', 'pegawais'));
    }

    /**
     * Proses Simpan Transaksi & Auto Generate PDF BAST Ramping (3NF)
     */
    public function store(Request $request, $lokasi)
    {
        // Validasi masih menggunakan 'name' bawaan form HTML, 
        // pastikan rule exists mengecek ke kolom database yang baru
        $validated = $request->validate([
            'peralatan_kode'    => 'required|exists:peralatans,alat_kode_barang', 
            'pegawai_id'        => 'required|exists:pegawais,pegawai_nip', // 🌟 REVISI: Acuan sekarang NIP
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

            // 🌟 MAPPING DATA KE TABEL BMDS
            $bmd = Bmd::create([
                'bmd_bast_nomor'        => $nomorOtomatis,
                'bmd_bast_tanggal'      => $bastTanggal,
                'bmd_alat_kode'         => $request->peralatan_kode,
                'bmd_pegawai_nip'       => $request->pegawai_id, // Walau namanya id, isinya nip
                'bmd_bendahara_nip'     => $request->bendahara_id,
                'bmd_pemakai_status'    => $request->pemakai_status,
                'bmd_pemakai_identitas' => $request->pemakai_identitas,
                'bmd_keterangan'        => $request->keterangan,
                'lokasi'                => $lokasi
            ]);

            // UPDATE STATUS KIB B Menjadi 'Aktif'
            Peralatan::where('alat_kode_barang', $request->peralatan_kode)->update([
                'alat_status_penggunaan' => 'Aktif'
            ]);

            // AMBIL DATA UTUH UNTUK INJEKSI DOMPDF
            $peralatanData = Peralatan::where('alat_kode_barang', $request->peralatan_kode)->first();
            $pegawaiData = Pegawai::where('pegawai_nip', $request->pegawai_id)->first();
            $bendaharaData = Pegawai::where('pegawai_nip', $request->bendahara_id)->first();

            if (!Storage::disk('public')->exists('uploads/bast')) {
                Storage::disk('public')->makeDirectory('uploads/bast', 0755, true);
            }

            // SUNTIK DATA KE PDF
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

    public function edit($lokasi, $id) // Gunakan ID Bmd secara eksplisit
    {
        $bmd = Bmd::findOrFail($id);
        if ($bmd->lokasi !== $lokasi) abort(404);

        $peralatans = Peralatan::where('lokasi', $lokasi)
            ->where(function($query) use ($bmd) {
                $query->where('alat_status_penggunaan', 'Tidak Aktif')
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
            // Sinkronisasi otomatis status penggunaan peralatan
            if ($bmd->bmd_alat_kode !== $request->peralatan_kode) {
                Peralatan::where('alat_kode_barang', $bmd->bmd_alat_kode)->update(['alat_status_penggunaan' => 'Tidak Aktif']);
                Peralatan::where('alat_kode_barang', $request->peralatan_kode)->update(['alat_status_penggunaan' => 'Aktif']);
            }

            // MAPPING UPDATE
            $bmd->update([
                'bmd_alat_kode'         => $request->peralatan_kode,
                'bmd_pegawai_nip'       => $request->pegawai_id, 
                'bmd_bendahara_nip'     => $request->bendahara_id,
                'bmd_pemakai_status'    => $request->pemakai_status,
                'bmd_pemakai_identitas' => $request->pemakai_identitas,
                'bmd_keterangan'        => $request->keterangan,
            ]);

            // Bersihkan berkas PDF lama 
            if ($bmd->bmd_bast_file && Storage::disk('public')->exists($bmd->bmd_bast_file)) {
                Storage::disk('public')->delete($bmd->bmd_bast_file);
            }

            // AMBIL DATA RE-GENERATE
            $peralatanData = Peralatan::where('alat_kode_barang', $request->peralatan_kode)->first();
            $pegawaiData = Pegawai::where('pegawai_nip', $request->pegawai_id)->first();
            $bendaharaData = Pegawai::where('pegawai_nip', $request->bendahara_id)->first();

            // Pembuatan ulang PDF BAST 
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
            // Kembalikan status peralatan 
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
        
        // 🌟 REVISI: Penamaan kolom relasi
        $namaBarang = $bmd->peralatan->alat_nama_barang ?? 'Aset'; 
        $namaPemakai = $bmd->pegawai->pegawai_nama ?? 'Pegawai';
        
        $pesan = $isDelete 
            ? "Data pemakaian {$namaPemakai} dihapus" 
            : "Aset {$namaBarang} dialokasikan kepada {$namaPemakai}";
            
        Notification::send($recipients, new DataModificationNotification(Auth::user(), $action, 'Penggunaan BMD', $pesan));
    }
}