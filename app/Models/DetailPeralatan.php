<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPeralatan extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Nama tabel
    protected $table = 'detail_peralatans';

    // 2. Primary Key kustom
    protected $primaryKey = 'dt_alat_id';

    // 3. Kolom fillable
    protected $fillable = [
        'dt_alat_kode_barang',
        'dt_alat_kode_barcode',
        'dt_alat_kondisi',
        'lokasi',
        'dt_alat_status_pinjam',
        'dt_alat_tanggal_cek',
        'dt_alat_foto',
        'dt_alat_keterangan',
    ];

    /**
     * Relasi ke Peralatan (KIB B)
     */
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'dt_alat_kode_barang', 'alat_kode_barang');
    }
}