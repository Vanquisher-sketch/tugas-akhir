<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_inventaris_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id(); // (5) No.

            // Kolom dipertahankan sesuai permintaan
            $table->string('lokasi'); 
            
            // Dibiarkan nullable untuk fleksibilitas jika 'lokasi' yang utama
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null');

            $table->string('nibar')->nullable(); // (6) NIBAR
            $table->string('nomor_register')->nullable(); // (7) Nomor Register
            $table->string('kode_barang'); // (8) Kode Barang
            $table->string('nama_barang'); // (9) Nama Barang
            $table->text('spesifikasi_barang')->nullable(); // (10) Spesifikasi
            $table->string('merk_tipe')->nullable(); // (11) Merek / Tipe (Tipe data diubah ke string)
            $table->year('tahun_perolehan'); // (12) Tahun Perolehan
            $table->unsignedInteger('jumlah'); // (13) Jumlah (Tipe data diubah ke integer)
            $table->string('satuan'); // (14) Satuan
            $table->text('keterangan')->nullable(); // (15) Ket.
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};