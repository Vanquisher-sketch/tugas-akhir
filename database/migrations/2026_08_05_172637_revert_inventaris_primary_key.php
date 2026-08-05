<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bersihkan data soft delete jika ada
        if (Schema::hasColumn('inventaris', 'deleted_at')) {
            DB::table('inventaris')->whereNotNull('deleted_at')->delete();
        }

        // 2. Lepas Primary Key lama jika ada
        try {
            Schema::table('inventaris', function (Blueprint $table) {
                $table->dropPrimary(['inv_kode_barang', 'inv_ruangan_kode']);
            });
        } catch (\Exception $e) {
            // Abaikan jika primary key belum terpasang
        }

        // 3. Pasang Composite Primary Key (3 Kolom: Kode + Ruangan + Kondisi)
        Schema::table('inventaris', function (Blueprint $table) {
            $table->primary(['inv_kode_barang', 'inv_ruangan_kode', 'inv_kondisi']);
        });
    }

    public function down(): void
    {
        Schema::table('inventaris', function (Blueprint $table) {
            $table->dropPrimary(['inv_kode_barang', 'inv_ruangan_kode', 'inv_kondisi']);
        });
    }
};