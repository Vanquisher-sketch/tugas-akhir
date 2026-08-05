<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaris', function (Blueprint $table) {
            // Hapus Primary Key 3 kolom lama
            $table->dropPrimary(['inv_kode_barang', 'inv_ruangan_kode', 'inv_kondisi']);
            
            // Buat Primary Key baru 2 kolom
            $table->primary(['inv_kode_barang', 'inv_ruangan_kode']);
        });
    }

    public function down(): void
    {
        Schema::table('inventaris', function (Blueprint $table) {
            $table->dropPrimary(['inv_kode_barang', 'inv_ruangan_kode']);
            $table->primary(['inv_kode_barang', 'inv_ruangan_kode', 'inv_kondisi']);
        });
    }
};