<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TambahFiturArsipKeSemuaTabel extends Migration
{
    public function up()
    {
        // Daftar tabel yang akan diberi fitur arsip
        $tabelAset = ['tanahs', 'peralatans', 'gedungs', 'jalans', 'rusaks', 'bmds', 'inventaris'];

        foreach ($tabelAset as $namaTabel) {
            if (Schema::hasTable($namaTabel)) {
                Schema::table($namaTabel, function (Blueprint $table) {
                   // $table->softDeletes(); // Menambah kolom deleted_at
                });
            }
        }
    }

    public function down()
    {
        $tabelAset = ['tanahs', 'peralatans', 'gedungs', 'jalans', 'rusaks', 'bmds', 'inventaris'];

        foreach ($tabelAset as $namaTabel) {
            if (Schema::hasTable($namaTabel)) {
                Schema::table($namaTabel, function (Blueprint $table) {
                    $table->dropSoftDeletes(); // Menghapus kolom deleted_at jika rollback
                });
            }
        }
    }
}