<?php

namespace App\Http\Controllers;

use App\Models\Peralatan;
use App\Models\DetailPeralatan;
use App\Models\Rusak;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PeralatanController extends Controller
{
    public function index(Request $request, $lokasi)
    {
        $search = $request->query('search');
        $query = Peralatan::where('lokasi', $lokasi);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('alat_nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('alat_kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('alat_nibar', 'LIKE', "%{$search}%")
                  ->orWhere('alat_merk_tipe', 'LIKE', "%{$search}%")
                  ->orWhere('alat_nomor_polisi', 'LIKE', "%{$search}%");
            });
        }
        
        $dataPeralatan = $query->latest('updated_at')->paginate(10);
        return view("pages.{$lokasi}.peralatan.index", compact('dataPeralatan', 'lokasi', 'search'));
    }

    public function create($lokasi)
    {
        return view("pages.{$lokasi}.peralatan.create", compact('lokasi'));
    }

    public function store(Request $request, $lokasi)
    {
        // 1. Membersihkan Format Rupiah dari Input Finansial
        $inputs = $request->all();
        $currencyFields = ['harga_satuan', 'nilai_perolehan'];

        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        // 2. Validasi Form Input
        $validator = Validator::make($request->all(), [
            'nama_barang'        => 'required|string|max:255',
            'nibr'               => 'nullable|string|max:255',
            'nomor_register'     => 'nullable|string|max:255',
            'Lok'                => 'required|string|max:255', // Nama Ruangan / Lokasi Fisik
            'merk_tipe'          => 'nullable|string|max:255',
            'spesifikasi_barang' => 'nullable|string',
            'spesifikasi_lainnya'=> 'nullable|string',
            'nomor_rangka'       => 'nullable|string|max:255',
            'nomor_polisi'       => 'nullable|string|max:255',
            'tanggal_pajak'      => 'nullable|date', 
            'tanggal_stnk'       => 'nullable|date', 
            'nomor_bpkb'         => 'nullable|string|max:255',
            'cara_perolehan'     => 'required|string|max:255',
            'tanggal_perolehan'  => 'required|date',
            'harga_satuan'       => 'required|numeric|min:0',
            'nilai_perolehan'    => 'required|numeric|min:0',
            'jumlah'             => 'required|integer|min:1',
            'satuan'             => 'required|string|max:50',
            'status_penggunaan'  => 'required|string|max:255',
            'kondisi'            => 'required|in:Baik,Rusak Ringan,Rusak Berat', 
            'keterangan'         => 'nullable|string',
            'foto'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return DB::transaction(function () use ($request, $lokasi) {

            // =========================================================================
            // 3. GENERATE KODE BARANG OTOMATIS DARI PARTIKEL (RUANGAN + BARANG + URUTAN)
            // =========================================================================

            // Partikel 1: Nama Ruangan (Inisial kata / Singkatan Uppercase)
            $kodeRuangan = $this->generateCodeParticle($request->Lok);

            // Partikel 2: Nama Barang (Inisial kata / Singkatan Uppercase)
            $kodeNamaBarang = $this->generateCodeParticle($request->nama_barang);

            // Prefix Gabungan (Contoh: RR-LPT)
            $prefix = "{$kodeRuangan}-{$kodeNamaBarang}";

            // Partikel 3: Susunan Inputan / Nomor Urut Sekuensial (4 digit)
            // FIX: Mengurutkan berdasarkan alat_kode_barang, bukan id
            $lastPeralatan = Peralatan::where('alat_kode_barang', 'LIKE', "{$prefix}-%")
                ->orderBy('alat_kode_barang', 'desc')
                ->first();

            if ($lastPeralatan) {
                // Ambil 4 angka terakhir dari kode_barang sebelumnya
                $lastNum = (int) substr($lastPeralatan->alat_kode_barang, -4);
                $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }

            // Kode Barang Otomatis Akhir (Contoh: RR-LPT-0001)
            $kodeBarangOtomatis = "{$prefix}-{$nextNum}";

            // =========================================================================

            // 4. Simpan Upload Foto
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store("peralatan/{$lokasi}", 'public');
            }
            
            // 5. Simpan Data Utama Peralatan (KIB B)
            $peralatan = Peralatan::create([
                'alat_kode_barang'        => $kodeBarangOtomatis,
                'alat_nama_barang'        => $request->nama_barang,
                'alat_nibar'              => $request->nibr,
                'alat_nomor_register'     => $request->nomor_register,
                'alat_lokasi_fisik'       => $request->Lok,
                'alat_merk_tipe'          => $request->merk_tipe,
                'alat_spesifikasi_barang' => $request->spesifikasi_barang,
                'alat_spesifikasi_lainnya'=> $request->spesifikasi_lainnya,
                'alat_nomor_rangka'       => $request->nomor_rangka,
                'alat_nomor_polisi'       => $request->nomor_polisi,
                'alat_tanggal_pajak'      => $request->tanggal_pajak,
                'alat_tanggal_stnk'       => $request->tanggal_stnk,
                'alat_nomor_bpkb'         => $request->nomor_bpkb,
                'alat_cara_perolehan'     => $request->cara_perolehan,
                'alat_tanggal_perolehan'  => $request->tanggal_perolehan,
                'alat_harga_satuan'       => $request->harga_satuan,
                'alat_nilai_perolehan'    => $request->nilai_perolehan,
                'alat_jumlah'             => $request->jumlah,
                'alat_satuan'             => $request->satuan,
                'alat_status_penggunaan'  => $request->status_penggunaan,
                'alat_kondisi'            => $request->kondisi,
                'alat_keterangan'         => $request->keterangan,
                'alat_foto'               => $fotoPath,
                'lokasi'                  => $lokasi,
            ]);

            // 6. Generate Unit Fisik Detail (Barcode/QR Code)
            for ($i = 1; $i <= (int)$request->jumlah; $i++) {
                $urutan = str_pad($i, 3, '0', STR_PAD_LEFT);
                DetailPeralatan::create([
                    'dt_alat_kode_barang'   => $peralatan->alat_kode_barang,
                    'dt_alat_kode_barcode'  => $peralatan->alat_kode_barang . '-' . $urutan,
                    'dt_alat_kondisi'       => $peralatan->alat_kondisi,
                    'lokasi'                => $lokasi,
                    'dt_alat_status_pinjam' => 'Tersedia',
                    'dt_alat_tanggal_cek'   => now(),
                ]);
            }

            // 7. Penanganan Kondisi Rusak Berat
            if ($peralatan->alat_kondisi === 'Rusak Berat') {
                Rusak::updateOrCreate(
                    ['rusak_kode_barang' => $peralatan->alat_kode_barang],
                    [
                        'rusak_jenis_asal' => 'Peralatan',
                        'rusak_keterangan' => $peralatan->alat_keterangan ?? 'Masuk otomatis dari modul Peralatan KIB B.',
                        'lokasi'           => $lokasi
                    ]
                );
            }

            // 8. Kirim Notifikasi Sistem
            $recipients = User::whereIn('user_role_id', [1, 2])->get();
            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new DataModificationNotification(
                    Auth::user(), 'ditambahkan', 'Peralatan (KIB B)', $peralatan->alat_nama_barang
                ));
            }

            return redirect()->route('lokasi.peralatan.index', ['lokasi' => $lokasi])
                             ->with('success', "Data Peralatan berhasil disimpan dengan Kode Barang: {$kodeBarangOtomatis}");
        });
    }

    /**
     * Helper Function: Membuat Singkatan/Partikel Kode dari Teks Input
     * Contoh: "Ruang Rapat Utama" -> "RRU"
     * Contoh: "Laptop" -> "LAP"
     */
    private function generateCodeParticle($text)
    {
        $cleanText = trim(preg_replace('/[^A-Za-z0-9\s]/', '', $text));
        $words = explode(' ', $cleanText);

        $code = '';

        if (count($words) > 1) {
            // Jika lebih dari 1 kata, ambil huruf pertama setiap kata (misal: Ruang Rapat -> RR)
            foreach ($words as $w) {
                if (!empty($w)) {
                    $code .= strtoupper(substr($w, 0, 1));
                }
            }
        } else {
            // Jika hanya 1 kata, ambil 3 huruf pertama (misal: Laptop -> LAP)
            $code = strtoupper(substr($cleanText, 0, 3));
        }

        return $code ?: 'BRG';
    }

    public function edit($lokasi, $kode_barang)
    {
        $peralatan = Peralatan::where('alat_kode_barang', $kode_barang)
            ->where('lokasi', $lokasi)
            ->firstOrFail();

        return view("pages.{$lokasi}.peralatan.edit", compact('peralatan', 'lokasi'));
    }

    public function update(Request $request, $lokasi, $kode_barang)
    {
        $peralatan = Peralatan::where('alat_kode_barang', $kode_barang)
            ->where('lokasi', $lokasi)
            ->firstOrFail();

        $inputs = $request->all();
        $currencyFields = ['harga_satuan', 'nilai_perolehan'];
        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        $validator = Validator::make($request->all(), [
            'nama_barang'        => 'required|string|max:255',
            'nibr'               => 'nullable|string|max:255',
            'nomor_register'     => 'nullable|string|max:255',
            'Lok'                => 'required|string', 
            'merk_tipe'          => 'nullable|string|max:255',
            'spesifikasi_barang' => 'nullable|string',
            'spesifikasi_lainnya'=> 'nullable|string',
            'nomor_rangka'       => 'nullable|string|max:255',
            'nomor_polisi'       => 'nullable|string|max:255',
            'tanggal_pajak'      => 'nullable|date', 
            'tanggal_stnk'       => 'nullable|date', 
            'nomor_bpkb'         => 'nullable|string|max:255',
            'cara_perolehan'     => 'required|string',
            'tanggal_perolehan'  => 'required|date',
            'harga_satuan'       => 'required|numeric|min:0',
            'nilai_perolehan'    => 'required|numeric|min:0',
            'jumlah'             => 'required|integer|min:1',
            'satuan'             => 'required|string',
            'status_penggunaan'  => 'required|string',
            'kondisi'            => 'required|in:Baik,Rusak Ringan,Rusak Berat', 
            'keterangan'         => 'nullable|string',
            'foto'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return DB::transaction(function () use ($request, $peralatan, $lokasi) {
            $fotoPath = $peralatan->alat_foto;
            if ($request->hasFile('foto')) {
                if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                    Storage::disk('public')->delete($fotoPath);
                }
                $fotoPath = $request->file('foto')->store("peralatan/{$lokasi}", 'public');
            }

            $peralatan->update([
                'alat_nama_barang'        => $request->nama_barang,
                'alat_nibar'              => $request->nibr,
                'alat_nomor_register'     => $request->nomor_register,
                'alat_lokasi_fisik'       => $request->Lok,
                'alat_merk_tipe'          => $request->merk_tipe,
                'alat_spesifikasi_barang' => $request->spesifikasi_barang,
                'alat_spesifikasi_lainnya'=> $request->spesifikasi_lainnya,
                'alat_nomor_rangka'       => $request->nomor_rangka,
                'alat_nomor_polisi'       => $request->nomor_polisi,
                'alat_tanggal_pajak'      => $request->tanggal_pajak,
                'alat_tanggal_stnk'       => $request->tanggal_stnk,
                'alat_nomor_bpkb'         => $request->nomor_bpkb,
                'alat_cara_perolehan'     => $request->cara_perolehan,
                'alat_tanggal_perolehan'  => $request->tanggal_perolehan,
                'alat_harga_satuan'       => $request->harga_satuan,
                'alat_nilai_perolehan'    => $request->nilai_perolehan,
                'alat_jumlah'             => $request->jumlah,
                'alat_satuan'             => $request->satuan,
                'alat_status_penggunaan'  => $request->status_penggunaan,
                'alat_kondisi'            => $request->kondisi,
                'alat_keterangan'         => $request->keterangan,
                'alat_foto'               => $fotoPath,
            ]);

            if ($peralatan->alat_kondisi === 'Rusak Berat') {
                Rusak::updateOrCreate(
                    ['rusak_kode_barang' => $peralatan->alat_kode_barang],
                    [
                        'rusak_jenis_asal' => 'Peralatan',
                        'rusak_keterangan' => $peralatan->alat_keterangan ?? 'Mengalami kerusakan berat operasional.',
                        'lokasi'           => $lokasi
                    ]
                );
            } else {
                Rusak::where('rusak_kode_barang', $peralatan->alat_kode_barang)->delete();
            }

            $recipients = User::whereIn('user_role_id', [1, 2])->get();
            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new DataModificationNotification(
                    Auth::user(), 'diperbarui', 'Peralatan (KIB B)', $peralatan->alat_nama_barang
                ));
            }

            return redirect()->route('lokasi.peralatan.index', ['lokasi' => $lokasi])
                             ->with('success', 'Data Peralatan berhasil diperbarui.');
        });
    }

    public function destroy($lokasi, $kode_barang)
    {
        $peralatan = Peralatan::where('alat_kode_barang', $kode_barang)
            ->where('lokasi', $lokasi)
            ->firstOrFail();

        $namaBarang = $peralatan->alat_nama_barang;
        
        DB::transaction(function () use ($peralatan) {
            Rusak::where('rusak_kode_barang', $peralatan->alat_kode_barang)->delete();
            DetailPeralatan::where('dt_alat_kode_barang', $peralatan->alat_kode_barang)->delete();
            $peralatan->delete();
        });

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'dihapus', 'Peralatan (KIB B)', $namaBarang
            ));
        }

        return redirect()->route('lokasi.peralatan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Peralatan berhasil dihapus.');
    }

    public function print($lokasi)
    {
        $dataPeralatan = Peralatan::where('lokasi', $lokasi)->latest('updated_at')->get();
        return view("pages.{$lokasi}.peralatan.print", compact('dataPeralatan', 'lokasi'));
    }

    public function show($lokasi, $kode_barang)
    {
        $peralatan = Peralatan::where('alat_kode_barang', $kode_barang)
            ->where('lokasi', $lokasi)
            ->firstOrFail();

        $jumlahSeharusnya = (int) $peralatan->alat_jumlah;
        $jumlahSekarang = DetailPeralatan::where('dt_alat_kode_barang', $kode_barang)->count();

        if ($jumlahSekarang < $jumlahSeharusnya) {
            DB::transaction(function () use ($jumlahSeharusnya, $jumlahSekarang, $kode_barang, $peralatan, $lokasi) {
                $selisih = $jumlahSeharusnya - $jumlahSekarang;
                $totalHistory = DetailPeralatan::withTrashed()->where('dt_alat_kode_barang', $kode_barang)->count();

                for ($i = 1; $i <= $selisih; $i++) {
                    $urutanBaru = str_pad($totalHistory + $i, 3, '0', STR_PAD_LEFT);
                    
                    DetailPeralatan::firstOrCreate(
                        ['dt_alat_kode_barcode' => $kode_barang . '-' . $urutanBaru],
                        [
                            'dt_alat_kode_barang'   => $kode_barang,
                            'dt_alat_kondisi'       => $peralatan->alat_kondisi ?? 'Baik', 
                            'lokasi'                => $lokasi,
                            'dt_alat_status_pinjam' => 'Tersedia',
                            'dt_alat_tanggal_cek'   => now(),
                        ]
                    );
                }
            });
        }

        $detailPeralatan = DetailPeralatan::where('dt_alat_kode_barang', $kode_barang)
                                        ->where('lokasi', $lokasi)
                                        ->orderBy('dt_alat_kode_barcode', 'asc')
                                        ->get();

        return view("pages.{$lokasi}.peralatan.show", compact('peralatan', 'detailPeralatan', 'lokasi'));
    }

    public function scan($lokasi, $barcode)
    {
        $detail = DetailPeralatan::where('dt_alat_kode_barcode', $barcode)
                                 ->where('lokasi', $lokasi)
                                 ->firstOrFail();

        $peralatan = Peralatan::where('alat_kode_barang', $detail->dt_alat_kode_barang)->firstOrFail();

        return view("pages.{$lokasi}.peralatan.scan", compact('detail', 'peralatan', 'lokasi'));
    }
}