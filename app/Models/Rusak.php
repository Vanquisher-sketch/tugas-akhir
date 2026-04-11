<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rusak extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'rusaks';

    // REVISI: Primary Key menggunakan no_id_pemda sesuai migration
    protected $primaryKey = 'no_id_pemda';

    // REVISI: Matikan auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_id_pemda', 'nama_barang', 'spesifikasi', 'no_polisi',
        'tahun_perolehan', 'harga_perolehan', 'kondisi',
        'tercatat_di_kib', 'keterangan', 'lokasi'
    ];
}