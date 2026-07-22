<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_peralatans', function (Blueprint $table) {
            // Primary Key diubah namanya agar spesifik dan unik
            $table->id('dt_alat_id'); 

            // 🌟 RELASI KE TABEL PERALATANS (KIB B)
            // Wajib string(30) agar sinkron dengan 'alat_kode_barang' di tabel peralatans
            $table->string('dt_alat_kode_barang', 30);
            $table->foreign('dt_alat_kode_barang')
                  ->references('alat_kode_barang')
                  ->on('peralatans')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Barcode/Serial Number satuan unit. Diberi batasan 50 karakter & fungsi unique() 
            // karena tidak boleh ada dua barang fisik dengan barcode yang sama persis
            $table->string('dt_alat_kode_barcode', 50)->unique(); 
            
            // Menggunakan Enum agar input kondisi fisik selalu seragam dan tidak ada typo
            $table->enum('dt_alat_kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik'); 
            
            // 🌟 TETAP MURNI 'lokasi' untuk jangkar filter sistem (disamakan ukurannya 30)
            $table->string('lokasi', 30); 
            
            // Menggunakan Enum untuk ketersediaan barang. Opsi disesuaikan dengan realita operasional
            $table->enum('dt_alat_status_pinjam', ['Tersedia', 'Dipinjam', 'Dalam Perawatan'])->default('Tersedia'); 
            
            $table->date('dt_alat_tanggal_cek'); 

            // 🌟 REVISI BARU: Tambahan untuk kelengkapan Audit Fisik 🌟
            // Menyimpan path/nama file foto kondisi fisik yang sebenarnya
            $table->string('dt_alat_foto', 255)->nullable();
            
            // Catatan spesifik untuk item ini (misal: "Kaca spion pecah" atau "Dipinjam Pak Camat")
            $table->text('dt_alat_keterangan')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peralatans');
    }
};