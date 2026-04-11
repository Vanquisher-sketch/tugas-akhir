<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jalan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'jalans';

    // REVISI: Primary Key menggunakan kode_barang
    protected $primaryKey = 'kode_barang';

    // REVISI: Matikan auto-increment karena PK adalah string
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_barang', 'lokasi', 'nama_barang', 'nibar', 'nomor_register',
        'spesifikasi_barang', 'spesifikasi_lainnya', 'nomor_ruas_jalan_jembatan_irigasi',
        'Lok', 'titik_koordinat', 'status_kepemilikan_tanah', 'jumlah',
        'satuan', 'harga_satuan', 'nilai_perolehan', 'cara_perolehan',
        'tanggal_perolehan', 'status_penggunaan', 'keterangan'
    ];
}