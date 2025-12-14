<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    /**
     * Kirim pesan WA menggunakan Fonnte.
     */
    public static function send($target, $message)
    {
        // 1. ISI TOKEN DI SINI (Dapat dari Dashboard Fonnte)
        // Contoh: $token = 'XyZ123abc456TokenAnda';
        $token = 'EJAWp7YsFdxF6tLEXYX9'; 

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target, // Nomor HP tujuan (format: 08xxx)
                'message' => $message,
                'countryCode' => '62', 
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Gagal kirim WA: " . $e->getMessage());
            return false;
        }
    }
}