<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventaris extends Model
{
    use HasFactory; // 🌟 PASTIKAN DI SINI SUDAH TIDAK ADA LAGI "use SoftDeletes;"
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
        'kondisi', // 🌟 Tetap masukkan fillable agar dibaca controller
        'keterangan'
    ];
}