<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rusak extends Model
{
    use SoftDeletes;
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
}