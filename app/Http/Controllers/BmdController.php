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
use Barryvdh\DomPDF\Facade\Pdf;

class BmdController extends Controller
{
    /**
     * Menampilkan daftar penggunaan BMD berdasarkan lokasi.
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');

        // Optimasi Query: Eager Load 'peralatan' dan filter lokasi
        $bmds = Bmd::with('peralatan')
            ->where('lokasi', $lokasi)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pemakai_nama', 'LIKE', "%{$search}%")
                      ->orWhere('alamat_penggunaan', 'LIKE', "%{$search}%")
                      ->orWhere('bast_nomor', 'LIKE', "%{$search}%")
                      ->orWhereHas('peralatan', function ($subQ) use ($search) {
                          $subQ->where('nama_barang', 'LIKE', "%{$search}%")
                               ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                               ->orWhere('nibr', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Agar pagination tetap membawa parameter search

        return view("pages.{$lokasi}.bmd.index", compact('bmds', 'lokasi', 'search'));
    }

    /**
     * Menampilkan form tambah data.
     */
    public function create($lokasi)
    {
        $peralatans = Peralatan::select('id', 'kode_barang', 'nama_barang', 'nibr')->get();
        return view("pages.{$lokasi}.bmd.create", compact('lokasi', 'peralatans'));
    }

    /**
     * Menyimpan data baru.
     */
    public function store(Request $request, $lokasi)
    {
        $validated = $this->validateRequest($request);

        DB::beginTransaction(); // Mulai Transaksi Database

        try {
            $dataToStore = $validated;
            $dataToStore['lokasi'] = $lokasi;

            // Handle Upload File
            if ($request->hasFile('bast_file')) {
                $dataToStore['bast_file'] = $this->handleFileUpload($request->file('bast_file'));
            }

            $bmd = Bmd::create($dataToStore);

            DB::commit(); // Simpan permanen jika sukses

            // Kirim Notifikasi (Diluar transaksi agar error notif tidak membatalkan simpan data)
            $this->sendNotification($bmd, 'ditambahkan');

            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])
                             ->with('success', 'Data Penggunaan BMD berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua jika error
            Log::error("Error Store BMD: " . $e->getMessage()); // Catat error di log
            
            // Hapus file jika sudah terlanjur terupload tapi DB gagal
            if (isset($dataToStore['bast_file'])) {
                $this->deleteFile($dataToStore['bast_file']);
            }

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Menampilkan form edit.
     */
    public function edit($lokasi, Bmd $bmd)
    {
        $this->checkLocationMatch($lokasi, $bmd);
        
        $peralatans = Peralatan::select('id', 'kode_barang', 'nama_barang', 'nibr')->get();
        return view("pages.{$lokasi}.bmd.edit", compact('bmd', 'lokasi', 'peralatans'));
    }

    /**
     * Update data.
     */
    public function update(Request $request, $lokasi, Bmd $bmd)
    {
        $this->checkLocationMatch($lokasi, $bmd);
        $validated = $this->validateRequest($request);

        DB::beginTransaction();

        try {
            $dataToUpdate = $validated;
            $oldFile = $bmd->bast_file;

            // Handle File Replace
            if ($request->hasFile('bast_file')) {
                // Upload file baru
                $dataToUpdate['bast_file'] = $this->handleFileUpload($request->file('bast_file'));
                
                // Tandai file lama untuk dihapus nanti
                $fileToDelete = $oldFile;
            }

            $bmd->update($dataToUpdate);

            DB::commit();

            // Hapus file lama fisik hanya jika update DB sukses & ada file baru
            if (isset($fileToDelete)) {
                $this->deleteFile($fileToDelete);
            }

            $this->sendNotification($bmd, 'diperbarui');

            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])
                             ->with('success', 'Data Penggunaan BMD berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Update BMD: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Hapus data.
     */
    public function destroy($lokasi, Bmd $bmd)
    {
        $this->checkLocationMatch($lokasi, $bmd);

        DB::beginTransaction();

        try {
            $filePath = $bmd->bast_file;
            $bmdDataForNotif = clone $bmd; // Clone object untuk keperluan notif setelah delete

            $bmd->delete();

            DB::commit();

            // Hapus file fisik setelah data DB sukses dihapus
            $this->deleteFile($filePath);

            // Notif
            $this->sendNotification($bmdDataForNotif, 'dihapus', true);

            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])
                             ->with('success', 'Data berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Delete BMD: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

    /**
     * Cetak PDF per lokasi.
     */
    public function print($lokasi)
    {
        $bmds = Bmd::with('peralatan')
                    ->where('lokasi', $lokasi)
                    ->latest()
                    ->get();

        $pdf = Pdf::loadView('bmd.pdf', compact('bmds', 'lokasi'))
                  ->setPaper('a4', 'landscape');
                  
        return $pdf->stream("Laporan-BMD-{$lokasi}.pdf");
    }

    // =========================================================================
    // PRIVATE METHODS (HELPER)
    // =========================================================================

    /**
     * Validasi Request (DRY Principle)
     */
    private function validateRequest(Request $request)
    {
        return $request->validate([
            // 1. Relasi & Lokasi
            'peralatan_id'       => 'required|exists:peralatans,id',
            'alamat_penggunaan'  => 'required|string',

            // 2. Data Pemakai Utama
            'pemakai_nama'       => 'required|string',
            'pemakai_status'     => 'required|string',
            'pemakai_identitas'  => 'required|string',
            'pemakai_jabatan'    => 'nullable|string',
            'pemakai_alamat'     => 'nullable|string',

            // 3. Data Kontak & Pajak (Sesuai Migration)
            'nomor_pemakai'      => 'nullable|numeric|digits_between:10,15',
            'nomor_bendahara'    => 'nullable|numeric|digits_between:10,15',
            'tanggal_pajak'      => 'nullable|date',

            // 4. Dokumen BAST
            'bast_nomor'         => 'nullable|string',
            'bast_tanggal'       => 'nullable|date',
            'bast_file'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

            // 5. Dokumen Lain
            'dokumen_lain_nama'    => 'nullable|string',
            'dokumen_lain_nomor'   => 'nullable|string',
            'dokumen_lain_tanggal' => 'nullable|date',

            // 6. Lainnya
            'keterangan'         => 'nullable|string',
        ]);
    }

    /**
     * Cek apakah BMD milik lokasi yang sedang diakses
     */
    private function checkLocationMatch($lokasi, $bmd)
    {
        if ($bmd->lokasi !== $lokasi) {
            abort(404, 'Data tidak ditemukan di lokasi ini.');
        }
    }

    /**
     * Handle File Upload
     */
    private function handleFileUpload($file)
    {
        return $file->store('uploads/bast', 'public');
    }

    /**
     * Handle File Delete (Aman dari error jika file tidak ada)
     */
    private function deleteFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Handle Kirim Notifikasi
     */
    private function sendNotification($bmd, $action, $isDelete = false)
    {
        try {
            $recipients = User::whereIn('role_id', [1, 2])->get();
            
            if ($isDelete) {
                $pesan = "Peminjam: {$bmd->pemakai_nama} (Data telah dihapus)";
            } else {
                $namaBarang = $bmd->peralatan->nama_barang ?? 'Barang';
                $pesan = "{$namaBarang} oleh {$bmd->pemakai_nama} di {$bmd->alamat_penggunaan}";
            }

            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 
                $action, 
                'Penggunaan BMD', 
                $pesan
            ));
        } catch (\Exception $e) {
            // Jangan biarkan error notifikasi mengganggu flow user
            Log::warning("Gagal mengirim notifikasi BMD: " . $e->getMessage());
        }
    }
}