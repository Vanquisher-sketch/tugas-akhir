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
    protected $description = 'Mengecek pajak kendaraan H-7 hingga Hari-H dan mengirim notifikasi email setiap hari ke Admin';

    /**
     * Eksekusi logika pengecekan di sini
     */
    public function handle()
    {
        // 1. KUNCI FIX: Paksa timezone ke WIB (Asia/Jakarta) agar Terminal dan Web 100% sinkron
        $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();
        $batasMaksimal = Carbon::now('Asia/Jakarta')->addDays(7)->endOfDay();

        // 2. KUNCI FIX: Gunakan whereBetween untuk mencari yang jatuh tempo dari HARI INI sampai H-7.
        // Dengan begini, admin akan dikirimkan email pengingat *setiap hari* selama rentang waktu 7 hari tersebut.
        $kendaraanJatuhTempo = Peralatan::whereNotNull('alat_tanggal_pajak')
                                        ->whereBetween('alat_tanggal_pajak', [$hariIni, $batasMaksimal])
                                        ->get();

        // 3. Jika tidak ada yang masuk radar H-7, hentikan proses
        if ($kendaraanJatuhTempo->isEmpty()) {
            $this->info('Aman. Tidak ada pajak kendaraan yang jatuh tempo dalam 7 hari ke depan.');
            return;
        }

        // 4. Jika ada, ambil data admin/user yang berhak menerima email (Role 1 dan 2)
        $penerima = User::whereIn('user_role_id', [1, 2])->get();
        
        if ($penerima->isEmpty()) {
            $this->error('Gagal kirim: Tidak ditemukan User dengan Role 1 atau 2.');
            return;
        }

        $jumlahTerkirim = 0;

        // 5. Looping data kendaraan dan kirim notifikasi ke masing-masing admin
        foreach ($kendaraanJatuhTempo as $kendaraan) {
            foreach ($penerima as $user) {
                $user->notify(new ReminderPajakNotification($kendaraan));
            }
            
            // Format tanggal yang rapi untuk ditampilkan di terminal
            $tglPajak = Carbon::parse($kendaraan->alat_tanggal_pajak)->format('d-m-Y');
            
            $this->info("Berhasil! Notifikasi terkirim untuk kendaraan: " . ($kendaraan->alat_nomor_polisi ?? 'Tanpa Plat') . " (Jatuh tempo: {$tglPajak})");
            $jumlahTerkirim++;
        }
        
        $this->info("--------------------------------------------------");
        $this->info("Total {$jumlahTerkirim} aset kendaraan berhasil diproses.");
    }
}