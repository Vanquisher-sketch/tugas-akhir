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
        Schema::create('rooms', function (Blueprint $table) {
            // 1. Hapus $table->id()
            
            // 2. Jadikan kode_ruangan sebagai Primary Key
            // Saya beri panjang 50 karakter agar fleksibel
            $table->string('kode_ruangan', 50)->primary(); 
            
            $table->string('lokasi'); 
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};