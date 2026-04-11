<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peralatan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'peralatans';

    // REVISI: Set Primary Key ke kode_barang
    protected $primaryKey = 'kode_barang';

    // REVISI: Matikan auto-increment karena PK adalah string
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_barang', 'lokasi', 'nama_barang', 'nibar', 'nomor_register',
        'spesifikasi_barang', 'merk_tipe', 'Lok', 'spesifikasi_lainnya',
        'nomor_polisi', 'nomor_rangka', 'nomor_bpkb', 'jumlah', 'satuan',
        'harga_satuan', 'nilai_perolehan', 'cara_perolehan', 
        'tanggal_perolehan', 'status_penggunaan', 'keterangan'
    ];
}