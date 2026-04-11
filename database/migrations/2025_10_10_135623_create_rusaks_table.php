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
        Schema::create('rusaks', function (Blueprint $table) {
    // REVISI: Primary Key menggunakan No ID Pemda atau kode unik rusak
    $table->string('no_id_pemda', 100)->primary(); 
    
    $table->string('nama_barang');
    $table->string('spesifikasi')->nullable();
    $table->string('no_polisi')->nullable();
    $table->year('tahun_perolehan')->nullable();
    $table->decimal('harga_perolehan', 15, 2)->nullable();
    $table->string('kondisi')->nullable();
    $table->string('tercatat_di_kib')->nullable(); // Contoh: KIB B
    $table->text('keterangan')->nullable();
    $table->string('lokasi');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rusaks');
    }
};
