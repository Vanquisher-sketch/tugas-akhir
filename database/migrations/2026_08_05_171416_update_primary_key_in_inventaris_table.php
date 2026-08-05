<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaris', function (Blueprint $table) {
            // 1. Hapus Primary Key lama (kombinasi inv_kode_barang & inv_ruangan_kode)
            $table->dropPrimary(['inv_kode_barang', 'inv_ruangan_kode']);

            // 2. Buat Primary Key baru gabungan 3 kolom
            $table->primary(['inv_kode_barang', 'inv_ruangan_kode', 'inv_kondisi']);
        });
    }

    public function down(): void
    {
        Schema::table('inventaris', function (Blueprint $table) {
            $table->dropPrimary(['inv_kode_barang', 'inv_ruangan_kode', 'inv_kondisi']);
            $table->primary(['inv_kode_barang', 'inv_ruangan_kode']);
        });
    }
};