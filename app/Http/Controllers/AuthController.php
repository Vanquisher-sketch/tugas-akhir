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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2, // Default role_id untuk 'User'
            'status' => 'submitted', // Status default saat mendaftar
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

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek status akun setelah login berhasil
            if ($user->status === 'submitted') {
                Auth::logout(); // Langsung logout kembali
                return back()->withErrors(['email' => 'Akun Anda masih menunggu persetujuan admin.'])->onlyInput('email');
            }

            if ($user->status === 'rejected') {
                Auth::logout(); // Langsung logout kembali
                return back()->withErrors(['email' => 'Akun Anda telah ditolak oleh admin.'])->onlyInput('email');
            }
            
            // Jika status 'approved', lanjutkan
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

            // Jika user adalah Admin, arahkan ke dashboard utama
            if ($user->name === 'Admin SINDI') {
                return redirect()->intended('dashboard');
            }

            // Jika user adalah user lokasi, arahkan ke halaman aset wilayahnya (contoh ke data tanah)
            if (array_key_exists($user->name, $locationMap)) {
                $lokasi = $locationMap[$user->name];
                return redirect()->route('lokasi.tanah.index', ['lokasi' => $lokasi]);
            }

            // Pengalihan default jika tidak ada peran khusus (misal user baru yang di-approve)
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

