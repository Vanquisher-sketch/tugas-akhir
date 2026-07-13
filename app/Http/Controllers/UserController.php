<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user.
     */
    public function index()
    {
        // Mengambil semua data user, diurutkan dari yang terbaru
        $users = User::latest()->paginate(10);
        
        return view('pages.user.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat user baru.
     */
    public function create()
    {
        return view('pages.user.create');
    }

    /**
     * Menyimpan data user baru ke dalam database.
     */
    public function store(Request $request)
    {
        // 1. Validasi input disesuaikan dengan nama kolom tabel yang baru
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,user_email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|integer', // Admin harus memilih role (1=Admin, 2=Kecamatan, 3=User)
            'status' => 'required|in:diajukan,disetujui,ditolak', // Admin menentukan status
        ]);

        // 2. Simpan menggunakan prefix 'user_'
        User::create([
            'user_nama' => $request->name,
            'user_email' => $request->email,
            'user_password' => Hash::make($request->password),
            'user_role_id' => $request->role_id,
            'user_status' => $request->status,
        ]);

        return redirect()->route('user.index')
                         ->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail dari satu user.
     */
    public function show(string $id)
    {
        // Menggunakan findOrFail dengan ID agar aman karena Primary Key kita kustom (user_id)
        $user = User::findOrFail($id);
        return view('pages.user.show', compact('user'));
    }

    /**
     * Menampilkan form untuk mengedit data user.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        // REVISI: Typo path view diperbaiki dari 'user.edit' menjadi 'pages.user.edit'
        return view('pages.user.edit', compact('user'));
    }

    /**
     * Mengupdate data user di dalam database (Termasuk fitur Approve/Reject).
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // 1. Validasi input (Pengecualian unique email diarahkan ke user_email dan user_id)
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,user_email,' . $user->user_id . ',user_id',
            'password' => 'nullable|string|min:8', 
            'role_id' => 'required|integer',
            'status' => 'required|in:diajukan,disetujui,ditolak',
        ]);
        
        // 2. Menyiapkan data yang akan diupdate dengan prefix 'user_'
        $dataToUpdate = [
            'user_nama' => $request->name,
            'user_email' => $request->email,
            'user_role_id' => $request->role_id,
            'user_status' => $request->status,
        ];
        
        // 3. Jika admin mengisi password baru, update passwordnya
        if ($request->filled('password')) {
            $dataToUpdate['user_password'] = Hash::make($request->password);
        }

        // 4. Eksekusi update
        $user->update($dataToUpdate);

        // REVISI: Typo route diperbaiki dari 'users.index' menjadi 'user.index'
        return redirect()->route('user.index')
                         ->with('success', 'Data user (dan status persetujuan) berhasil diperbarui.');
    }

    /**
     * Menghapus data user dari database.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user.index')
                         ->with('success', 'User berhasil dihapus.');
    }
}