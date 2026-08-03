<?php

namespace App\Http\Controllers;

use App\Models\Tanah;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TanahController extends Controller
{
    /**
     * Menampilkan daftar KIB A (Tanah)
     */
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        
        $dataTanah = Tanah::where('lokasi', $lokasi)
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('tanah_nama_barang', 'LIKE', "%{$search}%")
                      ->orWhere('tanah_lokasi_fisik', 'LIKE', "%{$search}%") 
                      ->orWhere('tanah_kode_barang', 'LIKE', "%{$search}%")
                      ->orWhere('tanah_bukti_nomor', 'LIKE', "%{$search}%");
                });
            })
            ->latest('updated_at')
            ->paginate(10);

        return view("pages.{$lokasi}.tanah.index", compact('dataTanah', 'lokasi', 'search'));
    }

    /**
     * Fitur Saran Pencarian (Autocomplete) - AJAX
     */
    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        
        $results = Tanah::where('lokasi', $lokasi)
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('tanah_nama_barang', 'LIKE', "%{$search}%")
                      ->orWhere('tanah_kode_barang', 'LIKE', "%{$search}%")
                      ->orWhere('tanah_lokasi_fisik', 'LIKE', "%{$search}%");
                });
            })
            ->limit(5)
            ->get(['tanah_nama_barang as label', 'tanah_kode_barang as value']);

        return response()->json($results);
    }

    public function create($lokasi)
    {
        return view("pages.{$lokasi}.tanah.create", compact('lokasi'));
    }

    /**
     * Simpan Data Tanah dengan Kode Barang Otomatis (Berbasis Partikel)
     */
    public function store(Request $request, $lokasi)
    {
        $this->cleanCurrencyInputs($request);

        // Validasi tanpa mewajibkan tanah_kode_barang (parameter kedua true = isStore)
        $validator = Validator::make($request->all(), $this->validationRules(null, true));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            DB::beginTransaction();

            $validatedData = $validator->validated();
            $validatedData['lokasi'] = $lokasi;
            
            // =========================================================================
            // LOGIKA KODE BARANG OTOMATIS PARTIKEL (KIB A)
            // (Partikel: Lokasi Fisik + Nama Barang + Nomor Urut Sekuensial)
            // =========================================================================
            
            // Partikel 1: Inisial Lokasi Fisik / Ruangan
            $kodeLokasi = $this->generateCodeParticle($request->tanah_lokasi_fisik);

            // Partikel 2: Inisial Nama Barang
            $kodeNamaBarang = $this->generateCodeParticle($request->tanah_nama_barang);

            // Prefix Gabungan (Contoh: KI-TL)
            $prefix = "{$kodeLokasi}-{$kodeNamaBarang}";

            // Partikel 3: Nomor Urut Sekuensial (4 digit)
            $lastRecord = Tanah::where('lokasi', $lokasi)
                ->where('tanah_kode_barang', 'LIKE', "{$prefix}-%")
                ->orderBy('id', 'desc')
                ->first();

            if ($lastRecord) {
                // Ambil 4 angka terakhir dari tanah_kode_barang
                $lastNum = (int) substr($lastRecord->tanah_kode_barang, -4);
                $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }

            // Hasil Kode Barang Otomatis (Contoh: KI-TL-0001)
            $validatedData['tanah_kode_barang'] = "{$prefix}-{$nextNum}";
            // =========================================================================

            $tanah = Tanah::create($validatedData);

            $this->sendNotification('ditambahkan', $tanah->tanah_nama_barang);

            DB::commit();
            return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                             ->with('success', 'Data Tanah berhasil ditambahkan dengan Kode Barang: ' . $validatedData['tanah_kode_barang']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Store Tanah: " . $e->getMessage());
            
            return redirect()->back()->withInput()
                             ->with('error', 'Gagal menyimpan data. Cek kembali isian Anda. (Error: ' . $e->getMessage() . ')');
        }
    }

    public function edit($lokasi, $kode_barang)
    {
        $tanah = Tanah::where('lokasi', $lokasi)->findOrFail($kode_barang);
        return view("pages.{$lokasi}.tanah.edit", compact('tanah', 'lokasi'));
    }

    /**
     * Update Data Tanah
     */
    public function update(Request $request, $lokasi, $kode_barang)
    {
        $tanah = Tanah::where('lokasi', $lokasi)->findOrFail($kode_barang);

        $this->cleanCurrencyInputs($request);

        $validator = Validator::make($request->all(), $this->validationRules($tanah->tanah_kode_barang));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            DB::beginTransaction();

            $validatedData = $validator->validated();
            $tanah->update($validatedData);

            $this->sendNotification('diperbarui', $tanah->tanah_nama_barang);

            DB::commit();
            return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                             ->with('success', 'Data Tanah berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Update Tanah: " . $e->getMessage());
            
            return redirect()->back()->withInput()
                             ->with('error', 'Gagal memperbarui data. (Error: ' . $e->getMessage() . ')');
        }
    }

    public function destroy($lokasi, $kode_barang)
    {
        try {
            $tanah = Tanah::where('lokasi', $lokasi)->findOrFail($kode_barang);
            $itemName = $tanah->tanah_nama_barang;
            
            $tanah->delete();

            $this->sendNotification('dihapus', $itemName);

            return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                             ->with('success', 'Data Tanah berhasil dihapus.');
                             
        } catch (\Exception $e) {
            Log::error("Error Delete Tanah: " . $e->getMessage());
            return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi])
                             ->with('error', 'Gagal menghapus data.');
        }
    }

    public function print($lokasi)
    {
        $dataTanah = Tanah::where('lokasi', $lokasi)->latest('updated_at')->get();
        return view("pages.{$lokasi}.tanah.print", compact('dataTanah', 'lokasi'));
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Helper Function: Membuat Singkatan/Partikel Kode dari Teks Input
     * Contoh: "Kawasan Industri Selatan" -> "KIS"
     * Contoh: "Tanah" -> "TAN"
     */
    private function generateCodeParticle($text)
    {
        $cleanText = trim(preg_replace('/[^A-Za-z0-9\s]/', '', $text));
        $words = explode(' ', $cleanText);

        $code = '';

        if (count($words) > 1) {
            // Jika lebih dari 1 kata, ambil huruf pertama setiap kata
            foreach ($words as $w) {
                if (!empty($w)) {
                    $code .= strtoupper(substr($w, 0, 1));
                }
            }
        } else {
            // Jika hanya 1 kata, ambil 3 huruf pertama
            $code = strtoupper(substr($cleanText, 0, 3));
        }

        return $code ?: 'TNH';
    }

    /**
     * Membersihkan format Rupiah & Ribuan
     */
    private function cleanCurrencyInputs(Request $request)
    {
        $inputs = $request->all();
        $currencyFields = ['tanah_nilai_perolehan', 'tanah_harga_satuan', 'tanah_jumlah'];

        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        
        $request->replace($inputs);
    }

    /**
     * Aturan validasi (dinamis untuk store & update)
     */
    private function validationRules($ignoreKodeBarang = null, $isStore = false)
    {
        $rules = [
            'tanah_nama_barang'              => 'required|string|max:100',
            'tanah_nibar'                    => 'nullable|string|max:30',
            'tanah_nomor_register'           => 'nullable|string|max:20',
            'tanah_spesifikasi_barang'       => 'nullable|string|max:255',
            'tanah_spesifikasi_lainnya'      => 'nullable|string|max:255',
            'tanah_jumlah'                   => 'required|numeric', 
            'tanah_satuan'                   => 'required|string|max:20',
            'tanah_lokasi_fisik'             => 'required|string|max:255',
            'tanah_titik_koordinat'          => 'nullable|string|max:50',
            'tanah_bukti_nama'               => 'nullable|string|max:50',
            'tanah_bukti_nomor'              => 'nullable|string|max:50',
            'tanah_bukti_tanggal'            => 'nullable|date',
            'tanah_nama_kepemilikan_dokumen' => 'nullable|string|max:100',
            'tanah_nilai_perolehan'          => 'required|numeric|min:0',
            'tanah_harga_satuan'             => 'nullable|numeric|min:0',
            'tanah_cara_perolehan'           => 'required|string|max:50',
            'tanah_tanggal_perolehan'        => 'required|date',
            'tanah_status_penggunaan'        => 'required|in:Digunakan Sendiri,Dipinjamkan,Disewakan,Tidak Digunakan',
            'tanah_keterangan'               => 'nullable|string',
        ];

        // Jika bukan saat store (misal update), maka kode barang tetap divalidasi
        if (!$isStore) {
            $kodeBarangRule = $ignoreKodeBarang 
                ? "required|string|max:30|unique:tanahs,tanah_kode_barang,{$ignoreKodeBarang},tanah_kode_barang"
                : 'required|string|max:30|unique:tanahs,tanah_kode_barang';
            $rules['tanah_kode_barang'] = $kodeBarangRule;
        }

        return $rules;
    }

    /**
     * Mengirim notifikasi ke admin/user terkait
     */
    private function sendNotification($action, $itemName)
    {
        $recipients = User::whereIn('user_role_id', [1, 2])->get(); 
        
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), $action, 'Tanah', $itemName
            ));
        }
    }
}