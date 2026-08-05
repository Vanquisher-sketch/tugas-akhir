<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventaris', function (Blueprint $table) {
            // Hapus primary key yang lama
            $table->dropPrimary();
            // Buat primary key baru yang melibatkan 3 kolom (Kode, Ruangan, Kondisi)
            $table->primary(['inv_kode_barang', 'inv_ruangan_kode', 'inv_kondisi']);
        });
    }

    public function down()
    {
        Schema::table('inventaris', function (Blueprint $table) {
            $table->dropPrimary();
            $table->primary(['inv_kode_barang', 'inv_ruangan_kode']);
        });
    }
};