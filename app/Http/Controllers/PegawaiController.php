<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    // 1. Tampilkan Halaman Utama (Daftar Pegawai berdasarkan Lokasi URL)
    public function index($lokasi)
    {
        // Menyaring data pegawai yang kolom lokasinya sesuai dengan lokasi di URL
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
            'nip'     => 'required|unique:pegawais,pegawai_nip|digits:18', // Validasi unik ke kolom pegawai_nip
            'nama'    => 'required|string|max:100',
            'jabatan' => 'required|string|max:50',
            'alamat'  => 'required|string|max:100',
            'no_hp'   => 'required|numeric|unique:pegawais,pegawai_no_hp|digits_between:10,15',
            'email'   => 'nullable|email|unique:pegawais,pegawai_email',
        ]);

        Pegawai::create([
            'pegawai_nip'     => $request->nip,
            'pegawai_nama'    => $request->nama,
            'pegawai_jabatan' => $request->jabatan,
            'pegawai_alamat'  => $request->alamat,
            'pegawai_no_hp'   => $request->no_hp,
            'pegawai_email'   => $request->email,
            'lokasi'          => $lokasi,
        ]);

        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil ditambahkan!');
    }

    // 4. Tampilkan Halaman Form Edit Pegawai
    public function edit($lokasi, $nip) 
    {
        // 🌟 REVISI: Cari spesifik menggunakan where('pegawai_nip') agar tidak mencari kolom 'id'
        $pegawai = Pegawai::where('pegawai_nip', $nip)->firstOrFail(); 
        
        return view("pages.{$lokasi}.pegawai.edit", compact('pegawai', 'lokasi'));
    }

    // 5. Proses Update Data dari Form Edit
    public function update(Request $request, $lokasi, $nip)
    {
        // Pastikan datanya ada terlebih dahulu
        $exists = Pegawai::where('pegawai_nip', $nip)->exists();
        if (!$exists) { abort(404); }

        $request->validate([
            'nip'     => 'required|digits:18|unique:pegawais,pegawai_nip,' . $nip . ',pegawai_nip',
            'nama'    => 'required|string|max:100', 
            'jabatan' => 'required|string|max:50',
            'alamat'  => 'required|string|max:100',
            'no_hp'   => 'required|numeric|digits_between:10,15|unique:pegawais,pegawai_no_hp,' . $nip . ',pegawai_nip',
            'email'   => 'nullable|email|unique:pegawais,pegawai_email,' . $nip . ',pegawai_nip',
        ]);

        // 🌟 REVISI: Gunakan Query Builder murni untuk update demi menghindari jebakan kolom 'id'
        Pegawai::where('pegawai_nip', $nip)->update([
            'pegawai_nip'     => $request->nip,
            'pegawai_nama'    => $request->nama,
            'pegawai_jabatan' => $request->jabatan,
            'pegawai_alamat'  => $request->alamat,
            'pegawai_no_hp'   => $request->no_hp,
            'pegawai_email'   => $request->email,
        ]);

        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil diperbarui!');
    }

    // 6. Proses Hapus Data
    public function destroy($lokasi, $nip)
    {
        // 🌟 REVISI: Hapus menggunakan Query Builder murni berbasis pegawai_nip
        Pegawai::where('pegawai_nip', $nip)->delete();

        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil dihapus!');
    }
}