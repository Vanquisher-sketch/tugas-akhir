<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class ReminderPajakNotification extends Notification
{
    use Queueable;

    protected $kendaraan;

    // Menerima data kendaraan yang pajaknya mau habis
    public function __construct($kendaraan)
    {
        $this->kendaraan = $kendaraan;
    }

    // Menentukan bahwa notifikasi dikirim via email dan disimpan di database (lonceng)
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    // Menyusun bentuk dan isi Email
    public function toMail($notifiable)
    {
        // Ubah format tanggal agar enak dibaca (contoh: 09-08-2026)
        $tanggalPajak = Carbon::parse($this->kendaraan->alat_tanggal_pajak)->format('d-m-Y');

        return (new MailMessage)
                    ->subject('⚠️ PENGINGAT: Pajak Kendaraan Jatuh Tempo')
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line('Ini adalah pengingat otomatis dari sistem PANDAWA.')
                    ->line('Pajak untuk kendaraan berikut akan jatuh tempo dalam 7 hari:')
                    ->line('🚗 Nama Kendaraan: ' . $this->kendaraan->alat_nama_barang)
                    ->line('🔢 Nomor Polisi: ' . $this->kendaraan->alat_nomor_polisi)
                    ->line('📅 Tanggal Jatuh Tempo: ' . $tanggalPajak)
                    ->action('Buka Aplikasi PANDAWA', url('/'))
                    ->line('Harap segera memproses perpanjangan pajak kendaraan tersebut.');
    }

    // Menyusun bentuk pesan untuk disimpan di tabel database (lonceng notifikasi)
    public function toArray($notifiable)
    {
        $tanggalPajak = Carbon::parse($this->kendaraan->alat_tanggal_pajak)->format('d-m-Y');
        
        return [
            'pesan' => "Pajak kendaraan {$this->kendaraan->alat_nama_barang} ({$this->kendaraan->alat_nomor_polisi}) jatuh tempo pada {$tanggalPajak}."
        ];
    }
}