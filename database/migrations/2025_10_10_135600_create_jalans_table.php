<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jalans', function (Blueprint $table) {
            // Primary Key dengan awalan 'jalan_' dan disusutkan ke 30
            $table->string('jalan_kode_barang', 30)->primary(); 
            
            // 🌟 TETAP MURNI 'lokasi' untuk kebutuhan filter (ukuran disamakan 30)
            $table->string('lokasi', 30);
            
            // Data Utama
            $table->string('jalan_nama_barang', 100); 
            $table->string('jalan_nibar', 30)->nullable(); 
            $table->string('jalan_nomor_register', 20); 
            
            // Spesifikasi diubah menjadi string(255) agar lebih optimal dari text
            $table->string('jalan_spesifikasi_barang', 255)->nullable(); 
            $table->string('jalan_spesifikasi_lainnya', 255)->nullable(); 
            
            // Atribut khusus KIB D (Jalan, Irigasi, Jaringan)
            $table->string('jalan_nomor_ruas_jalan_jembatan_irigasi', 50)->nullable(); 
            
            // Lokasi Fisik dan Titik Koordinat
            $table->string('jalan_lokasi_fisik', 255); 
            $table->string('jalan_titik_koordinat', 50)->nullable(); 
            $table->string('jalan_status_kepemilikan_tanah', 50)->nullable(); 
            
            // Jumlah dan Satuan
            $table->unsignedInteger('jalan_jumlah'); 
            $table->string('jalan_satuan', 20); 
            
            // Nilai Aset (Decimal 15,2 aman hingga skala triliunan)
            $table->decimal('jalan_harga_satuan', 15, 2); 
            $table->decimal('jalan_nilai_perolehan', 15, 2); 
            
            $table->string('jalan_cara_perolehan', 50); 
            $table->date('jalan_tanggal_perolehan'); 
            
            // 🌟 REVISI ENUM: Logika disesuaikan untuk KIB D
            $table->enum('jalan_status_penggunaan', [
                'Digunakan untuk Kepentingan Umum', 
                'Digunakan untuk Operasional', 
                'Dalam Perawatan', 
                'Tidak Digunakan'
            ])->default('Digunakan untuk Kepentingan Umum'); 
            
            $table->text('jalan_keterangan')->nullable(); 
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jalans');
    }
};