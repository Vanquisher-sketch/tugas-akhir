<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailInventaris extends Model
{
    use HasFactory;

    protected $table = 'detail_inventaris';
    protected $primaryKey = 'id_detail1';

    protected $fillable = [
        'id_barang',
        'kode_barcode',
        'kondisi',
        'lokasi',
        'status_pinjam',
        'tanggal_cek'
    ];

    // Relasi balik ke data induk Inventaris
    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'id_barang', 'kode_barang');
    }
}