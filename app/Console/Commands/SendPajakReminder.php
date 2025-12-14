<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bmd; // Pastikan Model Bmd sudah ada
use App\Services\WhatsappService;
use Carbon\Carbon;

class SendPajakReminder extends Command
{
    /**
     * Nama perintah yang akan diketik di terminal.
     */
    protected $signature = 'pajak:send-reminder';

    /**
     * Keterangan perintah.
     */
    protected $description = 'Kirim reminder WA ke Pemakai & Bendahara untuk pajak yang akan jatuh tempo';

    /**
     * Eksekusi logika utama.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan pajak di data BMD...');

        // 1. TENTUKAN WAKTU REMINDER (H-7 / 1 Minggu)
        // Cari yang jatuh tempo tepat 7 hari lagi dari sekarang
        $jatuhTempoNanti = Carbon::now()->addDays(7)->format('Y-m-d');

        // 2. CARI DATA DI TABEL BMD
        // Menggunakan kolom 'tanggal_pajak' sesuai Model Bmd Anda
        $daftarTagihan = Bmd::whereDate('tanggal_pajak', $jatuhTempoNanti)
                            ->with('peralatan') // Load data barang agar query ringan
                            ->get();

        if ($daftarTagihan->isEmpty()) {
            $this->info("Tidak ada pajak yang jatuh tempo pada tanggal $jatuhTempoNanti (7 hari lagi).");
            return;
        }

        $this->info("Ditemukan " . $daftarTagihan->count() . " aset yang pajaknya akan habis dalam 1 minggu.");

        // 3. LOOPING & KIRIM PESAN
        foreach ($daftarTagihan as $item) {
            
            // Ambil data aset dari relasi peralatan
            // Pastikan data peralatan ada, jika terhapus pakai default '-'
            $namaAset = $item->peralatan->nama_barang ?? 'Aset Tidak Dikenal';
            $nopol = $item->peralatan->nomor_polisi ?? '-';
            
            // Ambil lokasi (Prioritas: Lokasi fisik di BMD -> Lokasi sistem peralatan)
            $lokasi = $item->alamat_penggunaan ?? ($item->peralatan->Lok ?? '-');
            
            // Siapkan Data Penerima (Dari Model BMD yang Anda berikan)
            $targetNumbers = [];
            
            // Cek Nomor Pemakai
            if (!empty($item->nomor_pemakai)) {
                $targetNumbers['Pemakai'] = $item->nomor_pemakai;
            }
            
            // Cek Nomor Bendahara
            if (!empty($item->nomor_bendahara)) {
                $targetNumbers['Bendahara'] = $item->nomor_bendahara;
            }

            // Jika tidak ada nomor sama sekali, skip dan catat error
            if (empty($targetNumbers)) {
                $this->error("Skip aset '$namaAset': Tidak ada nomor WA di data BMD (nomor_pemakai/nomor_bendahara kosong).");
                continue;
            }

            // Susun Pesan
            $pesan = "*PENGINGAT PAJAK KENDARAAN*\n\n";
            $pesan .= "Halo,\n";
            $pesan .= "Informasi Kendaraan Dinas berikut akan jatuh tempo pajak dalam *1 Minggu (7 Hari)*:\n\n";
            $pesan .= "🔹 *Kendaraan:* $namaAset\n";
            $pesan .= "🔹 *No. Polisi:* $nopol\n";
            $pesan .= "🔹 *Lokasi:* $lokasi\n";
            $pesan .= "🔹 *Tgl Jatuh Tempo:* " . Carbon::parse($item->tanggal_pajak)->format('d-m-Y') . "\n";
            $pesan .= "\nMohon segera diproses administrasi perpajakannya.\n";
            $pesan .= "_Pengelolaan Data Aset Kecamatan Tawang (PANDAWA)_";

            // Kirim ke semua target (Pemakai & Bendahara)
            foreach ($targetNumbers as $role => $nomor) {
                // Pastikan format nomor '08xxx' atau '628xxx'
                $hasil = WhatsappService::send($nomor, $pesan);
                
                if ($hasil) {
                    $this->info("✅ Terkirim ke $role ($nomor) - Aset: $namaAset");
                } else {
                    $this->error("❌ Gagal kirim ke $role ($nomor) - Cek token Fonnte/Koneksi");
                }
            }
        }

        $this->info('Selesai pengecekan.');
    }
}