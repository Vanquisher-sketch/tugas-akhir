<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function login()
    {
        return view('pages.auth.login');
    }

    /**
     * Menampilkan halaman registrasi.
     */
    public function registerView()
    {
        return view('pages.auth.register');
    }

    /**
     * Memproses pendaftaran user baru.
     */
    public function register(Request $request)
    {
        // Asumsi form HTML kamu masih menggunakan name="name", name="email", dll.
        // Kita sesuaikan target tabel untuk validasi unique ke kolom 'user_email'
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,user_email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Simpan menggunakan nama kolom kustom kita
        User::create([
            'user_nama' => $request->name,
            'user_email' => $request->email,
            'user_password' => Hash::make($request->password),
            'user_role_id' => 3, // 🌟 REVISI: Di seeder kita, 3 adalah role untuk 'User' kelurahan
            'user_status' => 'diajukan', // 🌟 REVISI: Menggunakan bahasa Indonesia sesuai enum
        ]);

        return redirect()->route('login')->with('success', 'Berhasil mendaftar, akun Anda sedang menunggu persetujuan admin.');
    }

    /**
     * Memproses autentikasi user.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 🌟 KUNCI PENTING: Petakan input form ke kolom database kustom
        // Laravel mewajibkan key array 'password' tetap tertulis 'password' untuk pengecekan hash, 
        // tapi untuk email, kita arahkan ke 'user_email'
        $attemptData = [
            'user_email' => $credentials['email'],
            'password' => $credentials['password'], 
        ];

        // Jalankan proses login
        if (Auth::attempt($attemptData)) {
            $user = Auth::user();

            // Cek status akun menggunakan value baru
            if ($user->user_status === 'diajukan') {
                Auth::logout(); 
                return back()->withErrors(['email' => 'Akun Anda masih menunggu persetujuan admin.'])->onlyInput('email');
            }

            if ($user->user_status === 'ditolak') {
                Auth::logout(); 
                return back()->withErrors(['email' => 'Akun Anda telah ditolak oleh admin.'])->onlyInput('email');
            }
            
            // Jika status 'disetujui' (bawaan dari seeder), lanjutkan
            $request->session()->regenerate();

            // Peta untuk mengarahkan nama user ke slug lokasi
            $locationMap = [
                'Kecamatan Tawang' => 'tawang',
                'Kelurahan Lengkongsari' => 'lengkongsari',
                'Kelurahan Cikalang' => 'cikalang',
                'Kelurahan Empang' => 'empang',
                'Kelurahan Kahuripan' => 'kahuripan',
                'Kelurahan Tawangsari' => 'tawangsari',
            ];

            // 🌟 REVISI: Ganti pemanggilan $user->name menjadi $user->user_nama
            if ($user->user_nama === 'Admin SINDI') {
                return redirect()->intended('dashboard');
            }

            if (array_key_exists($user->user_nama, $locationMap)) {
                $lokasi = $locationMap[$user->user_nama];
                return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi]);
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}