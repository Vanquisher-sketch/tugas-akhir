<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tanahs', function (Blueprint $table) {
            $table->string('tanah_kode_barang', 30)->primary(); 
            
            $table->string('lokasi', 30)->nullable(); 

            $table->string('tanah_nama_barang', 100); 
            $table->string('tanah_nomor_register', 20)->nullable(); 
            
            $table->string('tanah_spesifikasi_barang', 255)->nullable(); 
            $table->string('tanah_spesifikasi_lainnya', 255)->nullable(); 
            
            $table->decimal('tanah_jumlah', 15, 2); 
            $table->string('tanah_satuan', 20); 
            
            $table->string('tanah_lokasi_fisik', 255); 
            $table->string('tanah_titik_koordinat', 50)->nullable(); 

            $table->string('tanah_bukti_nama', 50)->nullable(); 
            $table->string('tanah_bukti_nomor', 50)->nullable(); 
            $table->date('tanah_bukti_tanggal')->nullable(); 
            $table->string('tanah_nama_kepemilikan_dokumen', 100)->nullable(); 

            $table->decimal('tanah_nilai_perolehan', 15, 2); 
            $table->decimal('tanah_harga_satuan', 15, 2)->nullable(); 

            $table->string('tanah_cara_perolehan', 50); 
            $table->date('tanah_tanggal_perolehan'); 
            
            // 🌟 REVISI ENUM: Status Penggunaan dengan pilihan baku
            $table->enum('tanah_status_penggunaan', [
                'Digunakan Sendiri', 
                'Dipinjamkan', 
                'Disewakan', 
                'Tidak Digunakan'
            ])->default('Digunakan Sendiri'); 
            
            $table->text('tanah_keterangan')->nullable(); 
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanahs');
    }
};