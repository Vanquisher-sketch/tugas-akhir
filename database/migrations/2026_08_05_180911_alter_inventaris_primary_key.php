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
        Schema::table('inventaris', function (Blueprint $table) {
            // Hapus primary key lama (2 kolom)
            $table->dropPrimary(['inv_kode_barang', 'inv_ruangan_kode']);
            
            // Buat primary key baru (3 kolom)
            $table->primary(['inv_kode_barang', 'inv_ruangan_kode', 'inv_kondisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventaris', function (Blueprint $table) {
            // Kembalikan ke primary key lama (2 kolom)
            $table->dropPrimary(['inv_kode_barang', 'inv_ruangan_kode', 'inv_kondisi']);
            $table->primary(['inv_kode_barang', 'inv_ruangan_kode']);
        });
    }
};