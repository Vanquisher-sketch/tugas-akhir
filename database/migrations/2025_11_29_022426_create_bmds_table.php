<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bmds', function (Blueprint $table) {
            // Mengubah id() menjadi bmd_id agar unik sesuai aturan dosen
            $table->id('bmd_id'); 

            // 1. RELASI KIB B (Peralatan & Mesin)
            // Tipe data WAJIB string(30) karena mencocokkan alat_kode_barang di tabel peralatans
            $table->string('bmd_alat_kode', 30);
            $table->foreign('bmd_alat_kode')
                  ->references('alat_kode_barang')
                  ->on('peralatans')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // 2. RELASI DATA PEGAWAI (Sebagai Pemakai / Pihak Kedua)
            // Tipe data WAJIB string(20) karena di tabel pegawais, Primary Key-nya adalah pegawai_nip
            $table->string('bmd_pegawai_nip', 20);
            $table->foreign('bmd_pegawai_nip')
                  ->references('pegawai_nip')
                  ->on('pegawais')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // 3. RELASI DATA PEGAWAI (Sebagai Bendahara Barang Wilayah / Pihak Pertama)
            // Tipe data WAJIB string(20) mencocokkan pegawai_nip
            $table->string('bmd_bendahara_nip', 20)->nullable();
            $table->foreign('bmd_bendahara_nip')
                  ->references('pegawai_nip')
                  ->on('pegawais')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            // 4. FILTER UTAMA & IDENTITAS KONTEKSTUAL
            // 🌟 TETAP MURNI 'lokasi' untuk kebutuhan filter (ukuran disamakan 30)
            $table->string('lokasi', 30); 
            
            // Diubah menjadi enum agar pilihan statusnya konsisten (ASN atau Non-ASN)
            $table->enum('bmd_pemakai_status', ['ASN', 'Non-ASN']); 
            
            // Value 20 sudah sangat pas untuk menampung nomor NIP (18 digit) atau NIK (16 digit)
            $table->string('bmd_pemakai_identitas', 20); 

            // 5. DATA REGISTER DOKUMEN BAST OTOMATIS
            // Value 50 sangat cukup untuk format nomor surat resmi (contoh: 001/BAST/KEC-TWG/2026)
            $table->string('bmd_bast_nomor', 50); 
            $table->date('bmd_bast_tanggal'); 
            
            // Value 255 adalah standar terbaik untuk menyimpan path atau rute file PDF dokumen
            $table->string('bmd_bast_file', 255)->nullable(); 

            // 6. CATATAN & LOG DATA
            $table->text('bmd_keterangan')->nullable(); 
            
            $table->softDeletes(); 
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bmds');
    }
};