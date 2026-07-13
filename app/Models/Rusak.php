<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rusak extends Model
{
    protected $table = 'rusaks';

    // 🌟 TAMBAHKAN 3 BARIS INI AGAR LARAVEL TIDAK MENCARI 'id'
    protected $primaryKey = 'rusak_kode_barang';
    public $incrementing = false;
    protected $keyType = 'string';

    // Pastikan fillable-nya sudah menampung ini (sesuaikan dengan milikmu)
    protected $fillable = [
        'rusak_kode_barang',
        'rusak_jenis_asal',
        'rusak_keterangan',
        'lokasi'
    ];
