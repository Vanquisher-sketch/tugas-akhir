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
        Schema::create('bmds', function (Blueprint $table) {
            $table->id(); // ID Urut Transaksi Penggunaan

            // 1. RELASI KIB B (Peralatan & Mesin) - Berdasarkan Kode Barang Master
            $table->string('peralatan_kode', 100);
            $table->foreign('peralatan_kode')
                  ->references('kode_barang')
                  ->on('peralatans')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // 2. RELASI DATA PEGAWAI (Sebagai Pemakai / Pihak Kedua)
            $table->unsignedBigInteger('pegawai_id');
            $table->foreign('pegawai_id')
                  ->references('id')
                  ->on('pegawais')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // 3. RELASI DATA PEGAWAI (Sebagai Bendahara Barang Wilayah / Pihak Pertama)
            $table->unsignedBigInteger('bendahara_id')->nullable();
            $table->foreign('bendahara_id')
                  ->references('id')
                  ->on('pegawais')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            // 4. FILTER FILTER UTAMA & REKAM IDENTITAS KONTEKSTUAL
            $table->string('lokasi'); // 🌟 TETAP DIPERTAHANKAN (Untuk filter tawang, cikalang, dll)
            $table->string('pemakai_status'); // Status saat BAST dibuat (ASN / Non-ASN)
            $table->string('pemakai_identitas'); // NIP/NIK yang aktif saat BAST dibuat

            // 5. DATA REGISTER DOKUMEN BAST OTOMATIS
            $table->string('bast_nomor'); // Nomor Surat BAST Resmi hasil generate sistem
            $table->date('bast_tanggal'); // Tanggal Cetak / Penyerahan BAST
            $table->string('bast_file')->nullable(); // Path PDF BAST hasil simpan otomatis/upload scan

            // 6. CATATAN & LOG DATA
            $table->text('keterangan')->nullable(); // Catatan kondisi penempatan barang
            $table->softDeletes(); // Fitur pengaman data terhapus sementara
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bmds');
    }
};