<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    // 1. Tampilkan Halaman Utama (Daftar Pegawai berdasarkan Lokasi URL)
    public function index($lokasi)
    {
        // Menyaring data pegawai yang kolom lokasinya sesuai dengan lokasi di URL (misal: 'tawang', 'cikalang')
        $pegawai = Pegawai::where('lokasi', $lokasi)->get(); 
        
        // Mengarah dinamis ke folder: resources/views/pages/{lokasi}/pegawai/index.blade.php
        return view("pages.{$lokasi}.pegawai.index", compact('pegawai', 'lokasi'));
    }

    // 2. Tampilkan Halaman Form Tambah Pegawai
    public function create($lokasi)
    {
        // Mengarah dinamis ke folder: resources/views/pages/{lokasi}/pegawai/create.blade.php
        return view("pages.{$lokasi}.pegawai.create", compact('lokasi'));
    }

    // 3. Proses Simpan Data dari Form Tambah
    public function store(Request $request, $lokasi)
    {
        $validatedData = $request->validate([
            'nip' => 'nullable|unique:pegawais|digits:18',
            'nama' => 'required|string|max:100',
            'jabatan' => 'required|string|max:50',
            'no_hp' => 'required|numeric|unique:pegawais|digits_between:10,15',
            'email' => 'nullable|email|unique:pegawais',
        ]);

        // Menyisipkan string lokasi (dari URL) ke data yang akan disimpan ke database
        $validatedData['lokasi'] = $lokasi;

        Pegawai::create($validatedData);

        // Kembali ke rute index dengan membawa parameter lokasi
        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil ditambahkan!');
    }

    // 4. Tampilkan Halaman Form Edit Pegawai
    public function edit($lokasi, $id)
    {
        $pegawai = Pegawai::findOrFail($id); 
        
        // Mengarah dinamis ke folder: resources/views/pages/{lokasi}/pegawai/edit.blade.php
        return view("pages.{$lokasi}.pegawai.edit", compact('pegawai', 'lokasi'));
    }

    // 5. Proses Update Data dari Form Edit
    public function update(Request $request, $lokasi, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $validatedData = $request->validate([
            'nip' => 'nullable|digits:18|unique:pegawais,nip,' . $id,
            'nama' => 'string|max:100',
            'jabatan' => 'string|max:50',
            'no_hp' => 'numeric|digits_between:10,15|unique:pegawais,no_hp,' . $id,
            'email' => 'nullable|email|unique:pegawais,email,' . $id,
        ]);

        $pegawai->update($validatedData);

        // Kembali ke rute index dengan membawa parameter lokasi
        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil diperbarui!');
    }

    // 6. Proses Hapus Data
    public function destroy($lokasi, $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        // Kembali ke rute index dengan membawa parameter lokasi
        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil dihapus!');
    }
}