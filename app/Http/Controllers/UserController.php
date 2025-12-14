<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Mengambil semua data user, diurutkan dari yang terbaru, dan dibagi per 10 data per halaman
        $users = User::latest()->paginate(10);
        
        // Mengirim data users ke view 'users.index'
        return view('pages.user.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat user baru.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Langsung menampilkan view 'users.create'
        return view('pages.user.create');
    }

    /**
     * Menyimpan data user baru ke dalam database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // 2. Jika validasi berhasil, buat user baru
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password di-hash sebelum disimpan
        ]);

        // 3. Redirect ke halaman index dengan pesan sukses
        return redirect()->route('user.index')
                         ->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail dari satu user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // Menggunakan Route-Model Binding, Laravel otomatis mencari user berdasarkan ID
        return view('pages.user.show', compact('user'));
    }

    /**
     * Menampilkan form untuk mengedit data user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // Menggunakan Route-Model Binding
        return view('user.edit', compact('user'));
    }

    /**
     * Mengupdate data user di dalam database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            // Rule unique diabaikan untuk email user saat ini
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8', // Password boleh kosong
        ]);
        
        // 2. Menyiapkan data yang akan diupdate
        $dataToUpdate = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        // 3. Cek apakah ada input password baru, jika ada hash dan tambahkan ke data update
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        // 4. Update data user
        $user->update($dataToUpdate);

        // 5. Redirect ke halaman index dengan pesan sukses
        return redirect()->route('users.index')
                         ->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Menghapus data user dari database.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Menggunakan Route-Model Binding untuk mencari user lalu menghapusnya
        $user->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('user.index')
                         ->with('success', 'User berhasil dihapus.');
    }
}