<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_inventaris', function (Blueprint $table) {
            // id_detail1 (Primary Key - Auto Increment)
            $table->id('id_detail1'); 

            // id_barang (Foreign Key menghubungkan ke kode_barang di tabel inventaris)
            $table->string('id_barang', 100);
            $table->foreign('id_barang')
                  ->references('kode_barang')
                  ->on('inventaris')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->string('kode_barcode'); // Nomor seri / barcode unik per satuan unit
            $table->string('kondisi')->default('Baik'); // Baik, Rusak Ringan, Rusak Berat
            $table->string('lokasi'); // Posisi ruangan saat ini
            $table->string('status_pinjam')->default('Tersedia'); // Status ketersediaan
            $table->date('tanggal_cek'); // Tanggal pemeriksaan terakhir

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_inventaris');
    }
};