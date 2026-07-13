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
        $pegawai = Pegawai::where('lokasi', $lokasi)->get(); 
        
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
        // Validasi: arahkan pengecekan unik ke nama kolom database kita yang baru
        $request->validate([
            'nip' => 'required|unique:pegawais,pegawai_nip|digits:18', // 🌟 REVISI: Wajib diisi karena ini Primary Key
            'nama' => 'required|string|max:100',
            'jabatan' => 'required|string|max:50',
            'alamat' => 'required|string|max:100',
            'no_hp' => 'required|numeric|unique:pegawais,pegawai_no_hp|digits_between:10,15',
            'email' => 'nullable|email|unique:pegawais,pegawai_email',
        ]);

        // Simpan data dengan memetakan input form ke kolom 'pegawai_'
        Pegawai::create([
            'pegawai_nip' => $request->nip,
            'pegawai_nama' => $request->nama,
            'pegawai_jabatan' => $request->jabatan,
            'pegawai_alamat' => $request->alamat,
            'pegawai_no_hp' => $request->no_hp,
            'pegawai_email' => $request->email,
            'lokasi' => $lokasi, // Disisipkan otomatis dari URL
        ]);

        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil ditambahkan!');
    }

    // 4. Tampilkan Halaman Form Edit Pegawai
    public function edit($lokasi, $nip) // 🌟 REVISI: Ubah nama parameter dari $id jadi $nip agar logis
    {
        $pegawai = Pegawai::findOrFail($nip); 
        
        return view("pages.{$lokasi}.pegawai.edit", compact('pegawai', 'lokasi'));
    }

    // 5. Proses Update Data dari Form Edit
    public function update(Request $request, $lokasi, $nip)
    {
        $pegawai = Pegawai::findOrFail($nip);

        // Validasi Update: pengecualian unique harus sangat spesifik mencari PK pegawai_nip
        $request->validate([
            'nip' => 'required|digits:18|unique:pegawais,pegawai_nip,' . $nip . ',pegawai_nip',
            'nama' => 'required|string|max:100', // 🌟 Tambahkan required untuk keamanan
            'jabatan' => 'required|string|max:50',
            'alamat' => 'required|string|max:100',
            'no_hp' => 'required|numeric|digits_between:10,15|unique:pegawais,pegawai_no_hp,' . $nip . ',pegawai_nip',
            'email' => 'nullable|email|unique:pegawais,pegawai_email,' . $nip . ',pegawai_nip',
        ]);

        // Eksekusi pembaruan data
        $pegawai->update([
            'pegawai_nip' => $request->nip,
            'pegawai_nama' => $request->nama,
            'pegawai_jabatan' => $request->jabatan,
            'pegawai_alamat' => $request->alamat,
            'pegawai_no_hp' => $request->no_hp,
            'pegawai_email' => $request->email,
        ]);

        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil diperbarui!');
    }

    // 6. Proses Hapus Data
    public function destroy($lokasi, $nip)
    {
        $pegawai = Pegawai::findOrFail($nip);
        $pegawai->delete();

        return redirect()->route('lokasi.pegawai.index', $lokasi)
            ->with('success', 'Data pegawai berhasil dihapus!');
    }
}