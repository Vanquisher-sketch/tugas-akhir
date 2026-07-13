<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gedungs', function (Blueprint $table) {
            // Primary Key disusutkan ke 30 agar selaras dengan inventaris & tabel lain
            $table->string('gedung_kode_barang', 30)->primary();       
            
            // 🌟 TETAP MURNI 'lokasi' untuk kebutuhan filter (ukuran disamakan 30)
            $table->string('lokasi', 30);
            
            // Data Utama
            $table->string('gedung_nama_barang', 100);                      
            $table->string('gedung_nibar', 30)->nullable();                 
            $table->string('gedung_nomor_register', 20);                  
            
            // Spesifikasi menggunakan string(255) yang lebih ringan dari text
            $table->string('gedung_spesifikasi_barang', 255)->nullable();   
            $table->string('gedung_spesifikasi_lainnya', 255)->nullable();  
            
            // Integer sangat cukup untuk jumlah lantai
            $table->unsignedInteger('gedung_jumlah_lantai')->nullable(); 
            
            // Lokasi Fisik (Alamat) - 'Lok' diubah agar jelas dan tidak tabrakan dengan 'lokasi'
            $table->string('gedung_lokasi_fisik', 255);                     
            $table->string('gedung_titik_koordinat', 50)->nullable();       
            $table->string('gedung_status_kepemilikan_tanah', 50)->nullable(); 
            
            // Jumlah, Satuan, dan Nilai Aset
            $table->unsignedInteger('gedung_jumlah');                       
            $table->string('gedung_satuan', 20);                            
            $table->decimal('gedung_harga_satuan', 15, 2);                  
            $table->decimal('gedung_nilai_perolehan', 15, 2);               
            
            $table->string('gedung_cara_perolehan', 50);                    
            $table->date('gedung_tanggal_perolehan');                       
            
            // 🌟 REVISI ENUM: Status Penggunaan diselaraskan dengan tabel tanah
            $table->enum('gedung_status_penggunaan', [
                'Digunakan Sendiri', 
                'Dipinjamkan', 
                'Disewakan', 
                'Tidak Digunakan'
            ])->default('Digunakan Sendiri'); 
            
            $table->text('gedung_keterangan')->nullable();                  
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gedungs');
    }
};