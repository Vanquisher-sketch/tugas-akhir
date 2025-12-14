<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bmds', function (Blueprint $table) {
            $table->id();

            // 1. Relasi
            $table->foreignId('peralatan_id')->constrained('peralatans')->onDelete('cascade');

            // 2. Lokasi
            $table->string('lokasi');            
            $table->string('alamat_penggunaan'); 

            // 3. Data Pemakai
            $table->string('pemakai_nama');
            $table->string('pemakai_status');
            $table->string('pemakai_jabatan')->nullable();
            $table->string('pemakai_identitas'); 
            $table->text('pemakai_alamat')->nullable();

            // 4. Data Kontak & Pajak (KHUSUS MENU PAJAK)
            $table->string('nomor_pemakai', 20)->nullable();
            $table->string('nomor_bendahara', 20)->nullable();
            $table->date('tanggal_pajak')->nullable(); // <--- [BARU] Khusus Jatuh Tempo Pajak
            $table->date('tanggal_stnk')->nullable();

            // 5. Dokumen BAST (KHUSUS MENU BMD)
            $table->string('bast_nomor')->nullable();
            $table->date('bast_tanggal')->nullable(); // <--- Khusus Tanggal BAST
            $table->string('bast_file')->nullable();  

            // 6. Dokumen Lain
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