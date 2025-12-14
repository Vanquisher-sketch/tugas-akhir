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

        // A. Jika rolenya adalah Admin (role_id = 1), selalu izinkan.
        if ($user->role_id == 1) {
            return $next($request); // Lanjutkan ke controller
        }

        // B. Jika rolenya adalah Kecamatan (role_id = 2)
        if ($user->role_id == 2) {
            // Definisikan kelurahan mana saja yang berada di bawah kecamatan ini.
            // Anda bisa membuatnya lebih dinamis nanti jika ada lebih dari 1 kecamatan.
            $kelurahan_di_bawah_tawang = [
                'lengkongsari', 
                'cikalang', 
                'empang', 
                'kahuripan', 
                'tawangsari'
            ];

            // User Kecamatan Tawang boleh mengakses lokasinya sendiri ('tawang')
            // DAN semua lokasi kelurahan di bawahnya.
            if ($lokasiAkses === 'tawang' || in_array($lokasiAkses, $kelurahan_di_bawah_tawang)) {
                return $next($request); // Izinkan akses
            }
        }

        // C. Jika rolenya adalah Kelurahan (role_id = 3)
        if ($user->role_id == 3) {
            // Kita perlu mencocokkan nama user dengan lokasi yang diakses.
            // Contoh: User "Kelurahan Lengkongsari" harus cocok dengan lokasi "lengkongsari".
            // Kita ubah nama user menjadi format slug (lowercase, tanpa spasi dan "Kelurahan ")
            $userLokasi = strtolower(str_replace('Kelurahan ', '', $user->name));

            if ($lokasiAkses === $userLokasi) {
                return $next($request); // Izinkan akses jika lokasinya cocok
            }
        }

        // 3. Jika semua kondisi di atas tidak terpenuhi, tolak akses.
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses data wilayah ini.');
    }
}
