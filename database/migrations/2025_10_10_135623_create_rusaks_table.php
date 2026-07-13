<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rusaks', function (Blueprint $table) {
            // Mengubah id() menjadi rusak_id agar unik
            $table->id('rusak_id');
            
            // Disusutkan ke 30 karena kode_barang di tabel master (Peralatan, Gedung, dll) ukurannya 30
            $table->string('rusak_kode_barang', 30);
            
            // Value 50 sangat aman untuk menampung nama sumber tabel (misal: "Peralatan", "Gedung")
            $table->string('rusak_jenis_asal', 50);
            
            // Kolom penampung alasan kerusakan ditambahkan awalan agar unik
            $table->text('rusak_keterangan')->nullable(); 
            
            // 🌟 TETAP MURNI 'lokasi' untuk kebutuhan filter (ukuran disamakan 30)
            $table->string('lokasi', 30);
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rusaks');
    }
};