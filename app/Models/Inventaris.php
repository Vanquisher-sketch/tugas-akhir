<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventaris extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Tentukan Primary Key karena bukan pakai 'id' auto-increment
    protected $primaryKey = 'kode_barang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_barang',
        'lokasi',
        'room_kode',
        'nibar',
        'nomor_register',
        'nama_barang',
        'spesifikasi_barang',
        'merk_tipe',
        'tahun_perolehan',
        'jumlah',
        'satuan',
        'kondisi', 
        'keterangan'
    ];

    /**
     * 🌟 TAHAP 2 ROADMAP: KABEL RELASI KE MASTER PERALATAN (KIB B)
     * Menghubungkan kolom kode_barang di tabel inventaris ruangan 
     * ke kolom kode_barang di master data peralatan.
     */
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'kode_barang', 'kode_barang');
    }
}