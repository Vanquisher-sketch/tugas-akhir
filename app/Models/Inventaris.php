<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventaris extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'inventaris';

    // Set Primary Key ke kode_barang
    protected $primaryKey = 'kode_barang';

    // Matikan auto-increment
    public $incrementing = false;

    // Set tipe data string
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
        'keterangan',
    ];

    // Relasi ke Room
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_kode', 'kode_ruangan');
    }
}