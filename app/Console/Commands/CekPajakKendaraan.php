<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peralatan;
use App\Models\User;
use App\Notifications\ReminderPajakNotification;
use Carbon\Carbon;

class CekPajakKendaraan extends Command
{
    /**
     * Nama perintah yang akan dipanggil di terminal atau scheduler
     */
    protected $signature = 'pajak:cek';

    /**
     * Deskripsi singkat tentang command ini
     */
    protected $description = 'Mengecek pajak kendaraan H-7 dan mengirim notifikasi email ke Admin';

    /**
     * Eksekusi logika pengecekan di sini
     */
    public function handle()
    {
        // 1. Tentukan target tanggal (Hari ini + 7 Hari)
        $targetTanggal = Carbon::now()->addDays(7)->format('Y-m-d');

        // 2. Cari data Peralatan (KIB B) yang tanggal pajaknya = target tanggal
        $kendaraanJatuhTempo = Peralatan::whereNotNull('alat_tanggal_pajak')
                                        ->whereDate('alat_tanggal_pajak', $targetTanggal)
                                        ->get();

        // 3. Jika tidak ada yang jatuh tempo, hentikan proses
        if ($kendaraanJatuhTempo->isEmpty()) {
            $this->info('Aman. Tidak ada pajak kendaraan yang jatuh tempo 7 hari lagi.');
            return;
        }

        // 4. Jika ada, ambil data admin/user yang berhak menerima email (Role 1 dan 2)
        $penerima = User::whereIn('user_role_id', [1, 2])->get();

        // 5. Looping data kendaraan dan kirim notifikasi ke masing-masing admin
        foreach ($kendaraanJatuhTempo as $kendaraan) {
            foreach ($penerima as $user) {
                $user->notify(new ReminderPajakNotification($kendaraan));
            }
            $this->info("Berhasil! Notifikasi terkirim untuk kendaraan: {$kendaraan->alat_nomor_polisi}");
        }
    }
}