<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            // 1. Hapus $table->id()
            
            // 2. Jadikan kode_barang sebagai PRIMARY KEY (String)
            $table->string('kode_barang', 100)->primary(); 

            $table->string('lokasi'); 
            
            // Relasi ke ruangan menggunakan kode_ruangan (String)
            $table->string('room_kode')->nullable();
            $table->foreign('room_kode')
                  ->references('kode_ruangan')
                  ->on('rooms')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->string('nibar')->nullable(); 
            $table->string('nomor_register')->nullable(); 
            $table->string('nama_barang'); 
            $table->text('spesifikasi_barang')->nullable(); 
            $table->string('merk_tipe')->nullable(); 
            $table->year('tahun_perolehan'); 
            $table->unsignedInteger('jumlah'); 
            $table->string('satuan'); 
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->text('keterangan')->nullable(); 
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};