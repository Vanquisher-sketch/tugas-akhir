<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    use HasFactory;

    protected $table = 'gedungs';

    // REVISI: Set Primary Key ke kode_barang
    protected $primaryKey = 'kode_barang';

    // REVISI: Matikan auto-increment karena PK adalah string
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_barang', 'lokasi', 'nama_barang', 'nbar', 'nomor_register',
        'spesifikasi_barang', 'spesifikasi_lainnya', 'jumlah_lantai',
        'Lok', 'titik_koordinat', 'status_kepemilikan_tanah', 'jumlah',
        'satuan', 'harga_satuan', 'nilai_perolehan', 'cara_perolehan',
        'tanggal_perolehan', 'status_penggunaan', 'keterangan'
    ];
}