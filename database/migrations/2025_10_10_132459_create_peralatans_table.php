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
        Schema::create('peralatans', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi');
            $table->string('kode_barang');                   // Sesuai Kolom (6)
            $table->string('nama_barang');                  // Sesuai Kolom (7)
            $table->string('nibr')->nullable();             // Sesuai Kolom (8)
            $table->string('nomor_register');               // Sesuai Kolom (9)
            $table->string('spesifikasi_barang')->nullable(); // Sesuai Kolom (10)
            $table->string('merk_tipe')->nullable();        // Sesuai Kolom (11)
            $table->string('Lok');                       // Sesuai Kolom (12)
            $table->string('spesifikasi_lainnya')->nullable(); // Sesuai Kolom (13)

            // Kendaraan Dinas
            $table->string('nomor_polisi')->nullable();     // Sesuai Kolom (14)
            $table->string('nomor_rangka')->nullable();     // Sesuai Kolom (15)
            $table->string('nomor_bpkb')->nullable();       // Sesuai Kolom (16)

            $table->unsignedInteger('jumlah');              // Sesuai Kolom (17)
            $table->string('satuan');                       // Sesuai Kolom (18)
            $table->decimal('harga_satuan', 15, 2);         // Sesuai Kolom (19)
            $table->decimal('nilai_perolehan', 15, 2);      // Sesuai Kolom (20)
            $table->string('cara_perolehan');               // Sesuai Kolom (21)
            $table->date('tanggal_perolehan');              // Sesuai Kolom (22)
            $table->string('status_penggunaan')->nullable(); // Sesuai Kolom (23)
            $table->text('keterangan')->nullable();         // Sesuai Kolom (24)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peralatans');
    }
};