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
        Schema::create('jalans', function (Blueprint $table) {
    // REVISI: Hapus $table->id()
    $table->string('kode_barang', 100)->primary(); // (6) Primary Key

    $table->string('lokasi');
    $table->string('nama_barang'); // (7)
    $table->string('nibar')->nullable(); // (8)
    $table->string('nomor_register'); // (9)
    $table->string('spesifikasi_barang')->nullable(); // (10)
    $table->string('spesifikasi_lainnya')->nullable(); // (11)
    $table->string('nomor_ruas_jalan_jembatan_irigasi')->nullable(); // (12)
    $table->string('Lok'); // (13) Alamat/Lokasi Fisik
    $table->string('titik_koordinat')->nullable(); // (14)
    $table->string('status_kepemilikan_tanah')->nullable(); // (15)
    $table->unsignedInteger('jumlah'); // (16)
    $table->string('satuan'); // (17)
    $table->decimal('harga_satuan', 15, 2); // (18)
    $table->decimal('nilai_perolehan', 15, 2); // (19)
    $table->string('cara_perolehan'); // (20)
    $table->date('tanggal_perolehan'); // (21)
    $table->string('status_penggunaan')->nullable(); // (22)
    $table->text('keterangan')->nullable(); // (23)
    $table->softDeletes();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jalans');
    }
};