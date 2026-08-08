<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLokasiAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Dapatkan user yang sedang login
        $user = Auth::user();

        // 2. Dapatkan parameter {lokasi} dari URL yang sedang diakses
        // Contoh: jika URL-nya /tawang/room, maka $lokasiAkses akan bernilai 'tawang'
        $lokasiAkses = $request->route('lokasi');

        // Jika karena alasan tertentu parameter lokasi tidak ada, langsung tolak.
        if (!$user || !$lokasiAkses) {
            return redirect('/dashboard')->with('error', 'Akses tidak valid.');
        }

        // --- LOGIKA UTAMA HAK AKSES ---

        // A. Jika rolenya adalah Admin (user_role_id = 1), selalu izinkan.
        if ($user->user_role_id == 1) {
            return $next($request); // Lanjutkan ke controller
        }

        // B. Jika rolenya adalah Kecamatan (user_role_id = 2)
        if ($user->user_role_id == 2) {
            
            // User Kecamatan Tawang boleh mengakses lokasinya sendiri secara PENUH (CRUD)
            if ($lokasiAkses === 'tawang') {
                return $next($request);
            }

            // Definisikan kelurahan mana saja yang berada di bawah kecamatan ini.
            $kelurahan_di_bawah_tawang = [
                'lengkongsari', 
                'cikalang', 
                'empang', 
                'kahuripan', 
                'tawangsari'
            ];

            // Jika sedang mengakses lokasi kelurahan (Masuk mode Pemanatauan / Read-Only)
            if (in_array($lokasiAkses, $kelurahan_di_bawah_tawang)) {
                
                // Izinkan jika metode HTTP adalah GET (sekadar melihat) 
                // KECUALI jika URL-nya mengandung kata 'create' atau 'edit' (ingin membuka form input)
                if ($request->isMethod('GET') && !str_contains($request->path(), 'create') && !str_contains($request->path(), 'edit')) {
                    return $next($request); 
                }

                // Jika mencoba nge-POST (simpan), PUT (update), DELETE (hapus), atau buka form Create/Edit: Tolak!
                return redirect('/dashboard')->with('error', 'Akses Read-Only! Anda hanya dapat memantau data di wilayah ' . ucfirst($lokasiAkses) . ', tidak dapat menambah atau mengubahnya.');
            }
        }

        // C. Jika rolenya adalah Kelurahan (user_role_id = 3)
        if ($user->user_role_id == 3) {
            // Kita perlu mencocokkan nama user dengan lokasi yang diakses.
            // Contoh: User "Kelurahan Lengkongsari" harus cocok dengan lokasi "lengkongsari".
            $userLokasi = strtolower(str_replace('Kelurahan ', '', $user->name));

            if ($lokasiAkses === $userLokasi) {
                return $next($request); // Izinkan akses jika lokasinya cocok
            }
        }

        // 3. Jika semua kondisi di atas tidak terpenuhi, tolak akses.
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses data wilayah ini.');
    }
}