<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rusaks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 100);
            $table->string('jenis_asal', 50);
            $table->text('keterangan')->nullable(); // 🌟 Kolom baru penampung alasan kerusakan
            $table->string('lokasi', 100);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rusaks');
    }
};