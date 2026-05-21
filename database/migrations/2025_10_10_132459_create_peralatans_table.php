<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peralatans', function (Blueprint $table) {
            // Primary Key menggunakan kode_barang (String)
            $table->string('kode_barang', 100)->primary();   // Sesuai Kolom (6)
            
            $table->string('lokasi');
            $table->string('nama_barang');                   // Sesuai Kolom (7)
            $table->string('nibr')->nullable();             // Sesuai Kolom (8)
            $table->string('nomor_register');               // Sesuai Kolom (9)
            $table->string('spesifikasi_barang')->nullable(); // Sesuai Kolom (10)
            $table->string('merk_tipe')->nullable();        // Sesuai Kolom (11)
            $table->string('Lok');                          // Sesuai Kolom (12) - Lokasi Fisik
            $table->string('spesifikasi_lainnya')->nullable(); // Sesuai Kolom (13)

            // Kendaraan Dinas & Legalitas Pajak (Poin 4)
            $table->string('nomor_polisi')->nullable();     // Sesuai Kolom (14)
            $table->date('tanggal_pajak')->nullable();      // 🌟 TAMBAHAN POIN 4
            $table->date('tanggal_stnk')->nullable();       // 🌟 TAMBAHAN POIN 4
            $table->string('nomor_rangka')->nullable();     // Sesuai Kolom (15)
            $table->string('nomor_bpkb')->nullable();       // Sesuai Kolom (16)

            $table->unsignedInteger('jumlah');              // Sesuai Kolom (17)
            $table->string('satuan');                       // Sesuai Kolom (18)
            $table->decimal('harga_satuan', 15, 2);         // Sesuai Kolom (19)
            $table->decimal('nilai_perolehan', 15, 2);      // Sesuai Kolom (20)
            $table->string('cara_perolehan');               // Sesuai Kolom (21)
            $table->date('tanggal_perolehan');               // Sesuai Kolom (22)
            
            // Status Penggunaan & Kondisi Fisik (Poin 4 & 6)
            $table->string('status_penggunaan')->default('Tidak Aktif'); // 🌟 Sesuai Kolom (23) - Default Diubah Ke Tidak Aktif
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik'); // 🌟 TAMBAHAN POIN 6
            
            $table->text('keterangan')->nullable();          // Sesuai Kolom (24)
            
            $table->softDeletes(); // 🌟 Ditambahkan karena di Model kamu menggunakan trait SoftDeletes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peralatans');
    }
};