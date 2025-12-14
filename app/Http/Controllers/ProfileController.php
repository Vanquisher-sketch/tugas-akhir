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
        // PERBAIKAN: Ambil user segar dari database, bukan dari cache session
        $user = User::find(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        $user->save(); 

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password pengguna.
     */
    public function updatePassword(Request $request)
    {
        // PERBAIKAN UTAMA: Gunakan User::find() agar update benar-benar ke DB
        $user = User::find(Auth::id());

        // 1. Validasi input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // 2. Cek apakah password saat ini cocok
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        // 3. FORCE UPDATE: Gunakan update() langsung pada model database
        // Ini lebih aman daripada $user->save() untuk kasus ganti password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah! Silakan logout dan login ulang untuk menguji.');
    }
}