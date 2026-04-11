<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bmds', function (Blueprint $table) {
    $table->id(); // BMD tetap pakai ID karena satu barang bisa punya sejarah penggunaan berbeda

    // REVISI: Relasi menggunakan kode_barang (String), bukan peralatan_id (Integer)
    $table->string('peralatan_kode');
    $table->foreign('peralatan_kode')
          ->references('kode_barang')
          ->on('peralatans')
          ->onDelete('cascade')
          ->onUpdate('cascade');

    $table->string('lokasi');             
    $table->string('alamat_penggunaan'); 

    $table->string('pemakai_nama');
    $table->string('pemakai_status');
    $table->string('pemakai_jabatan')->nullable();
    $table->string('pemakai_identitas'); 
    $table->text('pemakai_alamat')->nullable();

    $table->string('nomor_pemakai', 20)->nullable();
    $table->string('nomor_bendahara', 20)->nullable();
    $table->date('tanggal_pajak')->nullable();
    $table->date('tanggal_stnk')->nullable();

    $table->string('bast_nomor')->nullable();
    $table->date('bast_tanggal')->nullable();
    $table->string('bast_file')->nullable();  

    $table->string('dokumen_lain_nama')->nullable();
    $table->string('dokumen_lain_nomor')->nullable();
    $table->date('dokumen_lain_tanggal')->nullable();

    $table->text('keterangan')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('bmds');
    }
};