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
            $table->id(); // ID Transaksi Penggunaan

            // 1. RELASI KIB B (Peralatan & Mesin) - Berdasarkan Kode Barang
            $table->string('peralatan_kode');
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

            // 4. INFORMASI LOKASI & TRANSAKSI BMD
            $table->string('lokasi'); // Menyimpan slug wilayah (misal: tawang, cikalang)
            $table->string('alamat_penggunaan'); // Detail titik fisik aset (misal: Ruang Camat)
            $table->string('pemakai_status'); // Tetap dipertahankan untuk status kontekstual (ASN / Non-ASN)
            $table->string('pemakai_identitas'); // Untuk mencatat NIP/NIK yang aktif saat BAST dibuat

            // 5. DOKUMEN SUMBER (BAST OTOMATIS)
            $table->string('bast_nomor'); // Nomor Surat BAST Resmi
            $table->date('bast_tanggal'); // Tanggal Penyerahan / TTD BAST
            $table->string('bast_file')->nullable(); // Path File PDF yang di-generate otomatis oleh sistem

            // 6. DOKUMEN LAIN (Jika Ada Lampiran Tambahan Manually)
            $table->string('dokumen_lain_nama')->nullable();
            $table->string('dokumen_lain_nomor')->nullable();
            $table->date('dokumen_lain_tanggal')->nullable();

            $table->text('keterangan')->nullable(); // Catatan kondisi fisik saat diserahkan
            $table->timestamps();
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