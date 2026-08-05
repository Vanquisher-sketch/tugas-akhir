<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            // 🌟 PERBAIKAN 1: Tambahkan ID standar bawaan Laravel untuk menghindari bug Eloquent
            $table->id(); 

            // Value ditekan ke 30 (Sangat cukup untuk kode aset resmi)
            $table->string('inv_kode_barang', 30); 

            // DIKEMBALIKAN: Murni 'lokasi' agar sistem filter berjalan lancar
            $table->string('lokasi', 30)->nullable(); 
            
            // Wajib 10 (Sesuai dengan tabel rooms)
            $table->string('inv_ruangan_kode', 10); 
            
            // 🌟 Relasi dikembalikan ke tabel 'ruangans'
            $table->foreign('inv_ruangan_kode')
                  ->references('kode_ruangan')
                  ->on('ruangans')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Nomor seri / register dipangkas agar database lebih ringan
            $table->string('inv_nomor_register', 20)->nullable(); 
            
            // Nama barang dipertahankan 100 untuk antisipasi nama yang panjang
            $table->string('inv_nama_barang', 100); 
            $table->text('inv_spesifikasi_barang')->nullable(); 
            $table->string('inv_merk_tipe', 50)->nullable(); 
            
            $table->year('inv_tahun_perolehan'); 
            // Integer bawaan sudah pas untuk jumlah
            $table->integer('inv_jumlah'); 
            
            // Value ditekan ke 20
            $table->string('inv_satuan', 20); 
            
            $table->enum('inv_kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->text('inv_keterangan')->nullable(); 
            
            $table->timestamps();
            $table->softDeletes();

            // 🌟 PERBAIKAN 2: Mengganti Primary Key menjadi Unique Index dengan menyertakan kondisi
            // Ini memungkinkan 1 barang di 1 ruangan yang sama memiliki 2 baris (satu 'Baik', satu 'Rusak')
            $table->unique(
                ['inv_kode_barang', 'inv_ruangan_kode', 'inv_kondisi'], 
                'inventaris_barang_ruangan_kondisi_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};