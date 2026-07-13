<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruangans', function (Blueprint $table) {
            // Value 10 sudah sangat pas untuk kode ruangan
            $table->string('kode_ruangan', 10)->primary(); 
            
            // 🌟 Sesuai request: murni 'lokasi' untuk kebutuhan filter
            $table->string('lokasi', 30); 

            // Value 50 untuk nama ruangan
            $table->string('ruangan_nama', 50);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangans');
    }
};