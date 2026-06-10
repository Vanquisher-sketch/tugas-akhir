<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tanahs', function (Blueprint $table) {
            // REVISI: Hapus $table->id()
            // REVISI: Jadikan kode_barang sebagai Primary Key
            $table->string('kode_barang', 100)->primary(); // (5)
            
            // Filter Sistem (tawang, kahuripan, dll)
            $table->string('lokasi')->nullable(); 

            // Data Utama Sesuai KIB A
            $table->string('nama_barang');                // (6)
            $table->string('nibar')->nullable();          // (7)
            $table->string('nomor_register')->nullable(); // (8)
            
            $table->text('spesifikasi_barang')->nullable();  // (9) Luas M2
            $table->text('spesifikasi_lainnya')->nullable(); // (10)
            
            $table->decimal('jumlah', 15, 2); // (11) Luas Tanah
            $table->string('satuan');         // (12) M2 / Ha
            
            // Lokasi Fisik (Alamat) - Menggunakan 'Lok' sesuai permintaanmu
            $table->text('Lok');              // (13) 
            $table->string('titik_koordinat')->nullable(); // (14)

            // Bukti Kepemilikan
            $table->string('bukti_nama')->nullable();    // (15) Sertifikat/Girik
            $table->string('bukti_nomor')->nullable();   // (16)
            $table->date('bukti_tanggal')->nullable();   // (17)
            $table->string('nama_kepemilikan_dokumen')->nullable(); // (18)

            // Nilai Aset
            $table->decimal('nilai_perolehan', 15, 2);       // (19)
            $table->decimal('harga_satuan', 15, 2)->nullable(); // (20)

            $table->string('cara_perolehan');    // (21)
            $table->date('tanggal_perolehan');   // (22)
            $table->string('status_penggunaan'); // (23)
            $table->text('keterangan')->nullable(); // (24)
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanahs');
    }
};