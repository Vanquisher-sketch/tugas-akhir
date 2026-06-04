<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rusak extends Model
{
    use HasFactory; // 🌟 PASTIKAN DI SINI TIDAK ADA LAGI "use SoftDeletes;"
    use SoftDeletes;
    
    protected $fillable = [
        'kode_barang',
        'jenis_asal',
        'keterangan',
        'lokasi'
    ];
}