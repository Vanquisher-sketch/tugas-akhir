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
// Pastikan kamu sudah install barryvdh/laravel-dompdf jika ingin pakai PDF
// atau gunakan view print biasa seperti menu lainnya.

class BmdController extends Controller
{
    /**
     * Menampilkan daftar penggunaan BMD (Monitoring BAST).
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');

        // REVISI: Eager Load menggunakan relasi 'peralatan' yang berbasis kode
        $bmds = Bmd::with('peralatan')
            ->where('lokasi', $lokasi)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pemakai_nama', 'LIKE', "%{$search}%")
                      ->orWhere('alamat_penggunaan', 'LIKE', "%{$search}%")
                      ->orWhere('bast_nomor', 'LIKE', "%{$search}%")
                      ->orWhereHas('peralatan', function ($subQ) use ($search) {
                          // REVISI: Pencarian menembus tabel peralatans menggunakan kode_barang
                          $subQ->where('nama_barang', 'LIKE', "%{$search}%")
                               ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                               ->orWhere('nibr', 'LIKE', "%{$search}%")
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
     * Form Tambah Data BMD.
     */
    public function create($lokasi)
    {
        // REVISI: Ambil kode_barang sebagai value untuk dropdown
        $peralatans = Peralatan::select('kode_barang', 'nama_barang', 'nibr')->get();
        return view("pages.{$lokasi}.bmd.create", compact('lokasi', 'peralatans'));
    }

    /**
     * Simpan Data.
     */
    public function store(Request $request, $lokasi)
    {
        $validated = $this->validateRequest($request);

        DB::beginTransaction();

        try {
            $dataToStore = $validated;
            $dataToStore['lokasi'] = $lokasi;

            // Handle Upload File BAST
            if ($request->hasFile('bast_file')) {
                $dataToStore['bast_file'] = $this->handleFileUpload($request->file('bast_file'));
            }

            $bmd = Bmd::create($dataToStore);

            DB::commit();

            $this->sendNotification($bmd, 'ditambahkan');

            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])
                             ->with('success', 'Data Penggunaan BMD berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Store BMD: " . $e->getMessage());
            
            if (isset($dataToStore['bast_file'])) {
                $this->deleteFile($dataToStore['bast_file']);
            }

            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($lokasi, Bmd $bmd)
    {
        $this->checkLocationMatch($lokasi, $bmd);
        
        // REVISI: Dropdown list menggunakan kode_barang
        $peralatans = Peralatan::select('kode_barang', 'nama_barang', 'nibr')->get();
        return view("pages.{$lokasi}.bmd.edit", compact('bmd', 'lokasi', 'peralatans'));
    }

    public function update(Request $request, $lokasi, Bmd $bmd)
    {
        $this->checkLocationMatch($lokasi, $bmd);
        $validated = $this->validateRequest($request);

        DB::beginTransaction();

        try {
            $dataToUpdate = $validated;

            if ($request->hasFile('bast_file')) {
                // Hapus file lama jika ada file baru
                $this->deleteFile($bmd->bast_file);
                $dataToUpdate['bast_file'] = $this->handleFileUpload($request->file('bast_file'));
            }

            $bmd->update($dataToUpdate);

            DB::commit();

            $this->sendNotification($bmd, 'diperbarui');

            return redirect()->route('lokasi.bmd.index', ['lokasi' => $lokasi])
                             ->with('success', 'Data Penggunaan BMD berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Update BMD: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function destroy($lokasi, Bmd $bmd)
    {
        $this->checkLocationMatch($lokasi, $bmd);

        DB::beginTransaction();

        try {
            $filePath = $bmd->bast_file;
            $bmdDataForNotif = clone $bmd; 

            $bmd->delete();

            DB::commit();

            $this->deleteFile($filePath);
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
     * Cetak Laporan BMD.
     */
    public function print($lokasi)
    {
        $bmds = Bmd::with('peralatan')
                    ->where('lokasi', $lokasi)
                    ->latest()
                    ->get();

        return view("pages.{$lokasi}.bmd.print", compact('bmds', 'lokasi'));
    }

    // =========================================================================
    // PRIVATE METHODS (HELPER)
    // =========================================================================

    private function validateRequest(Request $request)
    {
        return $request->validate([
            // REVISI: Validasi ke kode_barang di tabel peralatans
            'peralatan_kode'      => 'required|exists:peralatans,kode_barang',
            'alamat_penggunaan'   => 'required|string',
            'pemakai_nama'        => 'required|string',
            'pemakai_status'      => 'required|string',
            'pemakai_identitas'   => 'required|string',
            'pemakai_jabatan'     => 'nullable|string',
            'pemakai_alamat'      => 'nullable|string',
            'nomor_pemakai'       => 'nullable|string|max:20',
            'nomor_bendahara'     => 'nullable|string|max:20',
            'tanggal_pajak'       => 'nullable|date',
            'tanggal_stnk'        => 'nullable|date',
            'bast_nomor'          => 'nullable|string',
            'bast_tanggal'        => 'nullable|date',
            'bast_file'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_lain_nama'   => 'nullable|string',
            'dokumen_lain_nomor'  => 'nullable|string',
            'dokumen_lain_tanggal'=> 'nullable|date',
            'keterangan'          => 'nullable|string',
        ]);
    }

    private function checkLocationMatch($lokasi, $bmd)
    {
        if ($bmd->lokasi !== $lokasi) {
            abort(404, 'Data tidak ditemukan di lokasi ini.');
        }
    }

    private function handleFileUpload($file)
    {
        return $file->store('uploads/bast', 'public');
    }

    private function deleteFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function sendNotification($bmd, $action, $isDelete = false)
    {
        try {
            $recipients = User::whereIn('role_id', [1, 2])->get();
            
            if ($isDelete) {
                $pesan = "Data pemakaian oleh {$bmd->pemakai_nama} telah dihapus.";
            } else {
                // Karena pakai with('peralatan'), kita bisa ambil nama barangnya
                $namaBarang = $bmd->peralatan->nama_barang ?? 'Aset';
                $pesan = "Aset: {$namaBarang} digunakan oleh {$bmd->pemakai_nama}";
            }

            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 
                $action, 
                'Penggunaan BMD', 
                $pesan
            ));
        } catch (\Exception $e) {
            Log::warning("Gagal mengirim notifikasi BMD: " . $e->getMessage());
        }
    }
}