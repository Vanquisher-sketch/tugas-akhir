<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Roles (Peran)
        Schema::create('roles', function (Blueprint $table) {
            // Mengubah 'id' menjadi 'role_id' agar unik
            $table->id('role_id'); 
            
            // Value 50 sangat cukup untuk nama role seperti "Admin", "Petugas", dll.
            $table->string('role_nama', 50); 
            $table->timestamps();
        });

        // 2. Tabel Users (Pengguna)
        Schema::create('users', function (Blueprint $table) {
            // Mengubah 'id' menjadi 'user_id'
            $table->id('user_id'); 
            
            // Awalan 'user_' agar tidak sama dengan nama di tabel pegawai atau role
            $table->string('user_nama', 100);
            $table->string('user_email', 100)->unique();
            $table->timestamp('user_email_verified_at')->nullable();
            
            // Password WAJIB 255 karakter karena hasil enkripsi/hash Laravel itu panjang
            $table->string('user_password', 255); 
            
            // REVISI 4: Bahasa Indonesia untuk value status
            $table->enum('user_status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            
            // Foreign Key ke tabel roles (diberi nama user_role_id agar beda dari role_id)
            $table->unsignedBigInteger('user_role_id');
            
            // Custom remember token agar namanya spesifik
            $table->string('user_remember_token', 100)->nullable(); 
            $table->timestamps();

            // Setup relasi Foreign Key
            $table->foreign('user_role_id')->references('role_id')->on('roles')->onDelete('cascade');
        });

        // 3. Tabel Reset Password (SISTEM LARAVEL - Jangan diubah nama atributnya)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 100)->primary(); // Disesuaikan valuenya jadi 100
            $table->string('token', 255);
            $table->timestamp('created_at')->nullable();
        });

        // 4. Tabel Sessions (SISTEM LARAVEL - Jangan diubah nama atributnya)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index(); // Ini tetap user_id karena mengacu ke kolom user_id kita
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};