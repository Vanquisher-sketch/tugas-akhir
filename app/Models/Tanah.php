<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tanah extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $table = 'tanahs';

    // REVISI: Set Primary Key ke kode_barang
    protected $primaryKey = 'kode_barang';

    // REVISI: Matikan auto-increment karena PK adalah string
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_barang', 'lokasi', 'nama_barang', 'nibar', 'nomor_register',
        'spesifikasi_barang', 'spesifikasi_lainnya', 'jumlah', 'satuan',
        'Lok', 'titik_koordinat', 'bukti_nama', 'bukti_nomor', 
        'bukti_tanggal', 'nama_kepemilikan_dokumen', 'nilai_perolehan',
        'harga_satuan', 'cara_perolehan', 'tanggal_perolehan',
        'status_penggunaan', 'keterangan'
    ];
}