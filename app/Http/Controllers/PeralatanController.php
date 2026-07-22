<?php

namespace App\Http\Controllers;

use App\Models\Peralatan;
use App\Models\DetailPeralatan;
use App\Models\Rusak;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // 🌟 Ditambahkan untuk mengelola file foto

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

    public function autocomplete(Request $request, $lokasi)
    {
        $search = $request->query('term');
        $results = Peralatan::where('lokasi', $lokasi)
            ->where(function($q) use ($search) {
                $q->where('alat_nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('alat_kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('alat_merk_tipe', 'LIKE', "%{$search}%")
                  ->orWhere('alat_nomor_polisi', 'LIKE', "%{$search}%");
            })
            ->limit(8)
            ->get(['alat_nama_barang as label', 'alat_kode_barang as value']);

        return response()->json($results);
    }

    public function create($lokasi)
    {
        return view("pages.{$lokasi}.peralatan.create", compact('lokasi'));
    }

    public function store(Request $request, $lokasi)
    {
        $inputs = $request->all();
        $currencyFields = ['harga_satuan', 'nilai_perolehan', 'jumlah'];

        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        $validator = Validator::make($request->all(), [
            'kode_barang'        => 'required|string|max:100|unique:peralatans,alat_kode_barang',
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
            'cara_perolehan'     => 'required|string|max:255',
            'tanggal_perolehan'  => 'required|date',
            'harga_satuan'       => 'required|numeric|min:0',
            'nilai_perolehan'    => 'required|numeric|min:0',
            'jumlah'             => 'required|integer|min:1',
            'satuan'             => 'required|string|max:50',
            'status_penggunaan'  => 'required|string|max:255',
            'kondisi'            => 'required|in:Baik,Rusak Ringan,Rusak Berat', 
            'keterangan'         => 'nullable|string',
            'foto'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 🌟 Validasi foto
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 🌟 Proses Upload Foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            // Simpan foto di folder storage/app/public/peralatan/lokasi
            $fotoPath = $request->file('foto')->store("peralatan/{$lokasi}", 'public');
        }
        
        $peralatan = Peralatan::create([
            'alat_kode_barang'        => $request->kode_barang,
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
            'alat_foto'               => $fotoPath, // 🌟 Simpan path foto ke database
            'lokasi'                  => $lokasi,
        ]);

        if ($peralatan->alat_kondisi === 'Rusak Berat') {
            Rusak::updateOrCreate(
                ['rusak_kode_barang' => $peralatan->alat_kode_barang],
                [
                    'rusak_jenis_asal'  => 'Peralatan',
                    'rusak_keterangan'  => $peralatan->alat_keterangan ?? 'Masuk otomatis dari modul Peralatan KIB B.',
                    'lokasi'            => $lokasi
                ]
            );
        }

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'ditambahkan', 'Peralatan (KIB B)', $peralatan->alat_nama_barang
            ));
        }

        return redirect()->route('lokasi.peralatan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Peralatan berhasil ditambahkan.');
    }

    public function edit($lokasi, $kode_barang)
    {
        $peralatan = Peralatan::findOrFail($kode_barang);
        if ($peralatan->lokasi !== $lokasi) abort(404);
        return view("pages.{$lokasi}.peralatan.edit", compact('peralatan', 'lokasi'));
    }

    public function update(Request $request, $lokasi, $kode_barang)
    {
        $peralatan = Peralatan::findOrFail($kode_barang);
        if ($peralatan->lokasi !== $lokasi) abort(404);

        $inputs = $request->all();
        $currencyFields = ['harga_satuan', 'nilai_perolehan', 'jumlah'];
        foreach ($currencyFields as $field) {
            if (isset($inputs[$field])) {
                $cleanValue = str_replace('.', '', $inputs[$field]);
                $inputs[$field] = str_replace(',', '.', $cleanValue);
            }
        }
        $request->replace($inputs);

        $validator = Validator::make($request->all(), [
            'kode_barang'        => "required|string|max:100|unique:peralatans,alat_kode_barang,{$peralatan->alat_kode_barang},alat_kode_barang",
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
            'foto'               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 🌟 Validasi update foto
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 🌟 Proses Update Foto
        $fotoPath = $peralatan->alat_foto;
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            // Simpan foto baru
            $fotoPath = $request->file('foto')->store("peralatan/{$lokasi}", 'public');
        }

        $peralatan->update([
            'alat_kode_barang'        => $request->kode_barang,
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
            'alat_foto'               => $fotoPath, // 🌟 Update path foto
        ]);

        if ($peralatan->alat_kondisi === 'Rusak Berat') {
            Rusak::updateOrCreate(
                ['rusak_kode_barang' => $peralatan->alat_kode_barang],
                [
                    'rusak_jenis_asal'  => 'Peralatan',
                    'rusak_keterangan'  => $peralatan->alat_keterangan ?? 'Mengalami kerusakan berat operasional.',
                    'lokasi'            => $lokasi
                ]
            );
        } else {
            Rusak::where('rusak_kode_barang', $peralatan->alat_kode_barang)->delete();
        }

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(
                Auth::user(), 'diperbarui', 'Peralatan (KIB B)', $peralatan->alat_nama_barang
            ));
        }

        return redirect()->route('lokasi.peralatan.index', ['lokasi' => $lokasi])
                         ->with('success', 'Data Peralatan berhasil diperbarui.');
    }

    public function destroy($lokasi, $kode_barang)
    {
        $peralatan = Peralatan::findOrFail($kode_barang);
        if ($peralatan->lokasi !== $lokasi) abort(404);

        $namaBarang = $peralatan->alat_nama_barang;
        
        Rusak::where('rusak_kode_barang', $peralatan->alat_kode_barang)->delete();
        
        // 🌟 Foto tidak dihapus fisiknya di sini karena menggunakan SoftDeletes. 
        // Jika model di-restore nanti, fotonya akan tetap ada.
        $peralatan->delete();

        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
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
        // 1. Panggil data induk (KIB B)
        $peralatan = Peralatan::findOrFail($kode_barang);
        if ($peralatan->lokasi !== $lokasi) abort(404);

        // 2. 🌟 LOGIKA AUTO-GENERATE BARCODE OTOMATIS
        $jumlahSeharusnya = $peralatan->alat_jumlah;
        $jumlahSekarang = DetailPeralatan::where('dt_alat_kode_barang', $kode_barang)->count();

        // Jika jumlah barcode kurang dari jumlah induk, generate otomatis sisanya!
        if ($jumlahSekarang < $jumlahSeharusnya) {
            $selisih = $jumlahSeharusnya - $jumlahSekarang;
            
            // Hitung total history (termasuk yang di-soft-delete) agar nomor urut tidak bentrok
            $totalHistory = DetailPeralatan::withTrashed()->where('dt_alat_kode_barang', $kode_barang)->count();

            for ($i = 1; $i <= $selisih; $i++) {
                $urutanBaru = str_pad($totalHistory + $i, 3, '0', STR_PAD_LEFT);
                DetailPeralatan::create([
                    'dt_alat_kode_barang'   => $kode_barang,
                    'dt_alat_kode_barcode'  => $kode_barang . '-' . $urutanBaru,
                    'dt_alat_kondisi'       => $peralatan->alat_kondisi ?? 'Baik', 
                    'lokasi'                => $lokasi,
                    'dt_alat_status_pinjam' => 'Tersedia',
                    'dt_alat_tanggal_cek'   => now(),
                ]);
            }
        }

        // 3. Panggil data anak (Detail Peralatan) yang sudah ter-generate sempurna
        $detailPeralatan = DetailPeralatan::where('dt_alat_kode_barang', $kode_barang)
                                ->where('lokasi', $lokasi)
                                ->orderBy('dt_alat_kode_barcode', 'asc')
                                ->get();

        return view("pages.{$lokasi}.peralatan.show", compact('peralatan', 'detailPeralatan', 'lokasi'));
    }

    public function storeDetail(Request $request, $lokasi, $kode_barang)
    {
        // 1. Validasi Input Modal
        $request->validate([
            'dt_alat_kondisi'       => 'required|string',
            'dt_alat_status_pinjam' => 'required|string',
            'dt_alat_tanggal_cek'   => 'nullable|date'
        ]);

        // 2. GENERATE KODE QR OTOMATIS AMAN DARI SOFTDELETES
        $jumlahSekarang = DetailPeralatan::withTrashed()
                            ->where('dt_alat_kode_barang', $kode_barang)
                            ->count();
                            
        $urutanBaru = str_pad($jumlahSekarang + 1, 3, '0', STR_PAD_LEFT);
        $generatedQrCode = $kode_barang . '-' . $urutanBaru;

        // 3. Simpan ke Database
        DetailPeralatan::create([
            'dt_alat_kode_barang'   => $kode_barang,
            'dt_alat_kode_barcode'  => $generatedQrCode,
            'dt_alat_kondisi'       => $request->dt_alat_kondisi,
            'lokasi'                => $lokasi,
            'dt_alat_status_pinjam' => $request->dt_alat_status_pinjam,
            'dt_alat_tanggal_cek'   => $request->dt_alat_tanggal_cek,
        ]);

        return back()->with('success', 'Item fisik baru berhasil ditambahkan dengan QR Code otomatis!');
    }

    public function scan($lokasi, $barcode)
    {
        // 1. Cari data fisik anak berdasarkan barcode
        $detail = DetailPeralatan::where('dt_alat_kode_barcode', $barcode)
                                 ->where('lokasi', $lokasi)
                                 ->firstOrFail();

        // 2. Cari data induknya (KIB B)
        $peralatan = Peralatan::where('alat_kode_barang', $detail->dt_alat_kode_barang)->firstOrFail();

        // 3. Tampilkan ke halaman khusus mobile/scan
        return view("pages.{$lokasi}.peralatan.scan", compact('detail', 'peralatan', 'lokasi'));
    }
}