<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// REVISI: Nama class diubah menjadi RoleMiddleware
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles  // Parameter dinamis untuk peran
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Pastikan pengguna sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Cek apakah peran pengguna ada dalam daftar peran yang diizinkan
        if (in_array($user->role_id, $roles)) {
            return $next($request); // Lanjutkan permintaan jika peran sesuai
        }

        // Jika peran tidak sesuai, kembalikan ke dashboard dengan pesan error
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
