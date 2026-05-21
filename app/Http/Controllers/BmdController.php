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
                    $q->where('bast_nomor', 'LIKE', "%{$search}%")
                      ->orWhereHas('pegawai', function ($subQ) use ($search) {
                          $subQ->where('nama', 'LIKE', "%{$search}%");
                      })
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
     * Fitur Saran Pencarian (Autocomplete) via AJAX
     */
    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        $results = Bmd::with(['peralatan', 'pegawai'])
            ->where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('bast_nomor', 'LIKE', "%{$search}%")
                  ->orWhereHas('pegawai', function($subQ) use ($search) {
                      $subQ->where('nama', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('peralatan', function($subQ) use ($search) {
                      $subQ->where('nama_barang', 'LIKE', "%{$search}%");
                  });
            })
            ->limit(5)
            ->get();

        $formatted = $results->map(function($item) {
            $namaPemakai = $item->pegawai->nama ?? 'Tidak Diketahui';
            $namaBarang = $item->peralatan->nama_barang ?? 'Aset';
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
        // Hanya menampilkan peralatan yang berstatus 'Tidak Aktif' (Belum dipinjamkan)
        $peralatans = Peralatan::where('lokasi', $lokasi)
            ->where('status_penggunaan', 'Tidak Aktif')
            ->select('kode_barang', 'nama_barang', 'nomor_polisi')
            ->get();

        $pegawais = Pegawai::where('lokasi', $lokasi)->orderBy('nama')->get();

        return view("pages.{$lokasi}.bmd.create", compact('lokasi', 'peralatans', 'pegawais'));
    }

    /**
     * Proses Simpan Transaksi & Auto Generate PDF BAST & Auto Status Aktif Peralatan 🌟
     */
    public function store(Request $request, $lokasi)
    {
        // 1. Validasi Inputan Form Ramping
        $validated = $request->validate([
            'peralatan_kode'    => 'required|exists:peralatans,kode_barang',
            'pegawai_id'        => 'required|exists:pegawais,id',
            'bendahara_id'      => 'required|exists:pegawais,id',
            'alamat_penggunaan' => 'required|string|max:255',
            'pemakai_status'    => 'required|string',
            'pemakai_identitas' => 'required|string',
            'keterangan'        => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        try {
            $dataToStore = $validated;
            $dataToStore['lokasi'] = $lokasi;

            // 🌟 2. OTOMATISASI TANGGAL & PENOMORAN SURAT BAST DI LATAR BELAKANG
            $dataToStore['bast_tanggal'] = date('Y-m-d'); 
            $totalData = Bmd::where('lokasi', $lokasi)->count() + 1;
            $nomorOtomatis = str_pad($totalData, 3, '0', STR_PAD_LEFT) . '/BAST/' . strtoupper($lokasi) . '/' . date('Y');
            $dataToStore['bast_nomor'] = $nomorOtomatis;

            // 3. Simpan Transaksi BMD Utama ke Database
            $bmd = Bmd::create($dataToStore);

            // 🌟 4. OTOMATISASI STATUS PERALATAN KIB B -> Menjadi 'Aktif'
            Peralatan::where('kode_barang', $request->peralatan_kode)->update([
                'status_penggunaan' => 'Aktif'
            ]);

            // 🌟 5. AMBIL DATA UTUH SECARA TERPISAH (Solusi Anti-Blank Page DomPDF)
            $peralatanData = Peralatan::where('kode_barang', $request->peralatan_kode)->first();
            $pegawaiData = Pegawai::find($request->pegawai_id);
            $bendaharaData = Pegawai::find($request->bendahara_id);

            // 6. Integrasi Verifikasi Direktori Penyimpanan Berkas
            if (!Storage::disk('public')->exists('uploads/bast')) {
                Storage::disk('public')->makeDirectory('uploads/bast', 0755, true);
            }

            // 🌟 7. SUNTIK DATA MANDIRI KE VIEW CETAK BAST
            $pdf = Pdf::loadView("pages.{$lokasi}.bmd.cetak_bast", [
                'bast_nomor'        => $nomorOtomatis,
                'bast_tanggal'      => date('Y-m-d'),
                'lokasi'            => $lokasi,
                'alamat_penggunaan' => $request->alamat_penggunaan,
                'keterangan'        => $request->keterangan,
                'pemakai_identitas' => $request->pemakai_identitas,
                'pemakai_status'    => $request->pemakai_status,
                'peralatan'         => $peralatanData,
                'pegawai'           => $pegawaiData,
                'bendahara'         => $bendaharaData
            ]);

            $namaFile = 'BAST-' . strtoupper($lokasi) . '-' . $bmd->id . '-' . time() . '.pdf';
            $pathPenyimpanan = 'uploads/bast/' . $namaFile;

            // Simpan file fisik PDF ke Storage
            Storage::disk('public')->put($pathPenyimpanan, $pdf->output());

            // 8. Daftarkan path berkas file final ke kolom model bmd
            $bmd->update([
                'bast_file' => $pathPenyimpanan
            ]);

            DB::commit();

            $this->sendNotification($bmd, 'ditambahkan');
            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])
                ->with('success', 'Data pemakaian berhasil disimpan. Surat ' . $nomorOtomatis . ' otomatis diterbitkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memproses otomatisasi BMD: ' . $e->getMessage() . ' di file ' . $e->getFile() . ' baris ke-' . $e->getLine());
            return redirect()->back()->withInput()->with('error', 'Gagal memproses data. Alasan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan Form Edit Penggunaan BMD
     */
    public function edit($lokasi, Bmd $bmd)
    {
        if ($bmd->lokasi !== $lokasi) abort(404);

        $peralatans = Peralatan::where('lokasi', $lokasi)
            ->where(function($query) use ($bmd) {
                $query->where('status_penggunaan', 'Tidak Aktif')
                      ->orWhere('kode_barang', $bmd->peralatan_kode);
            })
            ->select('kode_barang', 'nama_barang', 'nomor_polisi')
            ->get();

        $pegawais = Pegawai::where('lokasi', $lokasi)->orderBy('nama')->get();

        return view("pages.{$lokasi}.bmd.edit", compact('bmd', 'lokasi', 'peralatans', 'pegawais'));
    }

    /**
     * Proses Perbarui Transaksi & Regenerate Ulang PDF BAST
     */
    public function update(Request $request, $lokasi, Bmd $bmd)
    {
        if ($bmd->lokasi !== $lokasi) abort(404);

        $validated = $request->validate([
            'peralatan_kode'    => 'required|exists:peralatans,kode_barang',
            'pegawai_id'        => 'required|exists:pegawais,id',
            'bendahara_id'      => 'required|exists:pegawais,id',
            'alamat_penggunaan' => 'required|string|max:255',
            'pemakai_status'    => 'required|string',
            'pemakai_identitas' => 'required|string',
            'keterangan'        => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 🌟 Sinkronisasi otomatis status penggunaan peralatan
            if ($bmd->peralatan_kode !== $request->peralatan_kode) {
                Peralatan::where('kode_barang', $bmd->peralatan_kode)->update(['status_penggunaan' => 'Tidak Aktif']);
                Peralatan::where('kode_barang', $request->peralatan_kode)->update(['status_penggunaan' => 'Aktif']);
            }

            // Update data transaksi
            $bmd->update($validated);

            // Bersihkan berkas PDF lama jika ada di storage fisik
            if ($bmd->bast_file && Storage::disk('public')->exists($bmd->bast_file)) {
                Storage::disk('public')->delete($bmd->bast_file);
            }

            // 🌟 AMBIL DATA UTUH MANDIRI TERPISAH UNTUK EDIT RE-GENERATE
            $peralatanData = Peralatan::where('kode_barang', $request->peralatan_kode)->first();
            $pegawaiData = Pegawai::find($request->pegawai_id);
            $bendaharaData = Pegawai::find($request->bendahara_id);

            // Pembuatan ulang PDF BAST dengan data terbaru
            $pdf = Pdf::loadView("pages.{$lokasi}.bmd.cetak_bast", [
                'bast_nomor'        => $bmd->bast_nomor,
                'bast_tanggal'      => $bmd->bast_tanggal,
                'lokasi'            => $lokasi,
                'alamat_penggunaan' => $request->alamat_penggunaan,
                'keterangan'        => $request->keterangan,
                'pemakai_identitas' => $request->pemakai_identitas,
                'pemakai_status'    => $request->pemakai_status,
                'peralatan'         => $peralatanData,
                'pegawai'           => $pegawaiData,
                'bendahara'         => $bendaharaData
            ]);
            
            $namaFile = 'BAST-' . strtoupper($lokasi) . '-' . $bmd->id . '-' . time() . '.pdf';
            $pathPenyimpanan = 'uploads/bast/' . $namaFile;

            Storage::disk('public')->put($pathPenyimpanan, $pdf->output());
            $bmd->update(['bast_file' => $pathPenyimpanan]);

            DB::commit();
            $this->sendNotification($bmd, 'diperbarui');
            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])->with('success', 'Data dan dokumen BAST berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui BMD: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Proses Hapus Data & Reset Status Penggunaan Peralatan Menjadi 'Tidak Aktif' 🌟
     */
    public function destroy($lokasi, Bmd $bmd)
    {
        if ($bmd->lokasi !== $lokasi) abort(404);
        
        $bmdDataForNotif = clone $bmd;
        
        DB::beginTransaction();
        try {
            // 🌟 Kembalikan status peralatan menjadi 'Tidak Aktif' agar bisa dipinjamkan lagi
            Peralatan::where('kode_barang', $bmd->peralatan_kode)->update([
                'status_penggunaan' => 'Tidak Aktif'
            ]);

            // Hapus berkas PDF fisik dari server
            if ($bmd->bast_file && Storage::disk('public')->exists($bmd->bast_file)) {
                Storage::disk('public')->delete($bmd->bast_file);
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

    /**
     * Membuka file PDF langsung di browser secara aman tanpa bypass web server rule 🌟
     */
    public function bukaPdf($lokasi, $id)
    {
        $bmd = Bmd::findOrFail($id);

        if (!Storage::disk('public')->exists($bmd->bast_file)) {
            abort(404, 'File BAST tidak ditemukan di server.');
        }

        return response()->file(storage_path('app/public/' . $bmd->bast_file), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($bmd->bast_file).'"'
        ]);
    }

    /**
     * Kirim Notifikasi Modifikasi Internal Projek
     */
    private function sendNotification($bmd, $action, $isDelete = false)
    {
        $recipients = User::whereIn('role_id', [1, 2])->get();
        
        $namaBarang = $bmd->peralatan->nama_barang ?? 'Aset';
        $namaPemakai = $bmd->pegawai->nama ?? 'Pegawai';
        
        $pesan = $isDelete 
            ? "Data pemakaian {$namaPemakai} dihapus" 
            : "Aset {$namaBarang} dialokasikan kepada {$namaPemakai}";
            
        Notification::send($recipients, new DataModificationNotification(Auth::user(), $action, 'Penggunaan BMD', $pesan));
    }
}