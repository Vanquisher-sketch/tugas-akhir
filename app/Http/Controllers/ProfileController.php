<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User; 

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman edit profil.
     */
    public function edit()
    {
        return view('pages.edit');
    }

    /**
     * Update informasi dasar profil pengguna.
     */
    public function update(Request $request)
    {
        // Auth::id() akan otomatis mengenali user_id berkat override di Model User
        $user = User::find(Auth::id());

        // 🌟 REVISI: Validasi dicocokkan dengan "name" di form blade (user_nama & user_email)
        $request->validate([
            'user_nama' => 'required|string|max:100',
            'user_email' => [
                'required',
                'string',
                'email',
                'max:100',
                // Arahkan cek unik ke user_email dan kecualikan user_id miliknya sendiri
                Rule::unique('users', 'user_email')->ignore($user->user_id, 'user_id'),
            ],
        ]);

        // 🌟 REVISI MAPPING: Tarik dari request sesuai form
        $user->user_nama = $request->user_nama;
        $user->user_email = $request->user_email;
        
        $user->save(); 

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password pengguna.
     */
    public function updatePassword(Request $request)
    {
        $user = User::find(Auth::id());

        // 1. Validasi input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // 2. Cek apakah password saat ini cocok
        if (!Hash::check($request->current_password, $user->user_password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        // 3. FORCE UPDATE: Gunakan update() langsung pada model database
        $user->update([
            'user_password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah! Silakan logout dan login ulang untuk menguji.');
    }
}