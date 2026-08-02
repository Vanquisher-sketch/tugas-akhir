<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    // 1. Tampilkan Halaman Utama
    public function index($lokasi)
    {
        $pegawai = Pegawai::where('lokasi', $lokasi)->orderBy('pegawai_nip', 'desc')->get(); 
        return view("pages.{$lokasi}.pegawai.index", compact('pegawai', 'lokasi'));
    }

    // 2. Tampilkan Halaman Form Tambah Pegawai
    public function create($lokasi)
    {
        return view("pages.{$lokasi}.pegawai.create", compact('lokasi'));
    }

    // 3. Proses Simpan Data dari Form Tambah
    public function store(Request $request, $lokasi)
    {
        $request->validate([
            'pegawai_nip'     => 'required|unique:pegawais,pegawai_nip|digits:18', 
            'pegawai_nama'    => 'required|string|max:100',
            'pegawai_jabatan' => 'required|string|max:50',
            'pegawai_alamat'  => 'required|string|max:100',
            // ✅ KITA KEMBALIKAN KE pegawai_no_hp (dengan format unik yang benar pakai KOMA)
            'pegawai_no_hp'   => 'required|numeric|unique:pegawais,pegawai_no_hp|digits_between:10,15',
            // ✅ KITA KEMBALIKAN KE pegawai_email
            'pegawai_email'   => 'nullable|email|unique:pegawais,pegawai_email', 
        ]);

        Pegawai::create([
            'pegawai_nip'     => $request->pegawai_nip,
            'pegawai_nama'    => $request->pegawai_nama,
            'pegawai_jabatan' => $request->pegawai_jabatan,
            'pegawai_alamat'  => $request->pegawai_alamat,
            // ✅ KITA KEMBALIKAN KE pegawai_no_hp
            'pegawai_no_hp'   => $request->pegawai_no_hp,
            // ✅ KITA KEMBALIKAN KE pegawai_email
            'pegawai_email'   => $request->pegawai_email,
            'lokasi'          => $lokasi,
        ]);

        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil ditambahkan!');
    }

    // 4. Tampilkan Halaman Form Edit Pegawai
    public function edit($lokasi, $nip) 
    {
        $pegawai = Pegawai::where('pegawai_nip', $nip)->firstOrFail(); 
        return view("pages.{$lokasi}.pegawai.edit", compact('pegawai', 'lokasi'));
    }

    // 5. Proses Update Data dari Form Edit
    public function update(Request $request, $lokasi, $nip)
    {
        $exists = Pegawai::where('pegawai_nip', $nip)->exists();
        if (!$exists) { abort(404); }

        $request->validate([
            'pegawai_nip'     => 'required|digits:18|unique:pegawais,pegawai_nip,' . $nip . ',pegawai_nip',
            'pegawai_nama'    => 'required|string|max:100', 
            'pegawai_jabatan' => 'required|string|max:50',
            'pegawai_alamat'  => 'required|string|max:100',
            // ✅ KITA KEMBALIKAN KE pegawai_no_hp
            'pegawai_no_hp'   => 'required|numeric|digits_between:10,15|unique:pegawais,pegawai_no_hp,' . $nip . ',pegawai_nip',
            // ✅ KITA KEMBALIKAN KE pegawai_email
            'pegawai_email'   => 'nullable|email|unique:pegawais,pegawai_email,' . $nip . ',pegawai_nip',
        ]);

        Pegawai::where('pegawai_nip', $nip)->update([
            'pegawai_nip'     => $request->pegawai_nip,
            'pegawai_nama'    => $request->pegawai_nama,
            'pegawai_jabatan' => $request->pegawai_jabatan,
            'pegawai_alamat'  => $request->pegawai_alamat,
            // ✅ KITA KEMBALIKAN KE pegawai_no_hp
            'pegawai_no_hp'   => $request->pegawai_no_hp,
            // ✅ KITA KEMBALIKAN KE pegawai_email
            'pegawai_email'   => $request->pegawai_email,
        ]);

        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil diperbarui!');
    }

    // 6. Proses Hapus Data
    public function destroy($lokasi, $nip)
    {
        Pegawai::where('pegawai_nip', $nip)->delete();
        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil dihapus!');
    }
}