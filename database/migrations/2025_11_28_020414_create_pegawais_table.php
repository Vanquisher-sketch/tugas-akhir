<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            // REVISI 21: NIP resmi menjadi Primary Key
            $table->string('pegawai_nip', 20)->primary(); 

            // Awalan 'pegawai_' untuk membedakan dengan tabel lain
            $table->string('pegawai_nama', 100);
            $table->string('pegawai_jabatan', 50);
            $table->string('pegawai_nohp', 15)->unique();
            $table->string('pegawai_alamat', 255); 
            $table->string('pegawai_email', 100)->unique()->nullable();

            // 🌟 Sesuai request: tetap 'lokasi' tanpa awalan
            $table->string('lokasi', 50); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};