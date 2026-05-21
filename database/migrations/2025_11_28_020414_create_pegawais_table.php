<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique()->nullable();
            $table->string('nama', 100);
            $table->string('jabatan', 50);
            $table->string('no_hp', 15)->unique();
            $table->string('email')->unique()->nullable();

        // 🌟 Tambahkan kolom ini agar filter lokasi bekerja
            $table->string('lokasi', 30); 

            $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};