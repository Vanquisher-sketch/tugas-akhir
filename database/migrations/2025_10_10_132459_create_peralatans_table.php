<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peralatans', function (Blueprint $table) {
            // Primary Key dengan awalan 'alat_' dan disusutkan ke 30 (sama dengan inventaris)
            $table->string('alat_kode_barang', 30)->primary(); 
            
            // 🌟 TETAP MURNI 'lokasi' untuk kebutuhan filter (ukuran disamakan 30)
            $table->string('lokasi', 30);
            
            // Awalan 'alat_' agar unik dan value ditekan agar efisien
            $table->string('alat_nama_barang', 100); 
            $table->string('alat_nibar', 30)->nullable(); 
            $table->string('alat_nomor_register', 20); 
            $table->string('alat_spesifikasi_barang', 255)->nullable(); 
            $table->string('alat_merk_tipe', 50)->nullable(); 
            
            // Atribut 'Lok' diubah menjadi 'alat_lokasi_fisik' agar tidak rancu dengan filter
            $table->string('alat_lokasi_fisik', 50); 
            $table->string('alat_spesifikasi_lainnya', 255)->nullable(); 

            // Kendaraan Dinas & Legalitas Pajak (Value dirampingkan)
            $table->string('alat_nomor_polisi', 20)->nullable(); // Nomor plat cukup 20
            $table->date('alat_tanggal_pajak')->nullable(); 
            $table->date('alat_tanggal_stnk')->nullable(); 
            $table->string('alat_nomor_rangka', 50)->nullable(); // VIN/Rangka biasanya 17 digit
            $table->string('alat_nomor_bpkb', 50)->nullable(); 

            $table->unsignedInteger('alat_jumlah'); 
            $table->string('alat_satuan', 20); // Satuan cukup 20
            
            // Decimal 15,2 sudah standar aman untuk nominal harga uang (hingga triliunan)
            $table->decimal('alat_harga_satuan', 15, 2); 
            $table->decimal('alat_nilai_perolehan', 15, 2); 
            
            $table->string('alat_cara_perolehan', 50); 
            $table->date('alat_tanggal_perolehan'); 
            
            // Status Penggunaan & Kondisi (Value dirampingkan)
            $table->string('alat_status_penggunaan', 30)->default('Tidak Aktif'); 
            $table->enum('alat_kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik'); 
            
            $table->text('alat_keterangan')->nullable(); 
            
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peralatans');
    }
};