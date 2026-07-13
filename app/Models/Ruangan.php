<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// 🌟 NAMA CLASS WAJIB DIUBAH JADI 'Ruangan'
class Ruangan extends Model
{
    use HasFactory;

    // 🌟 Sesuaikan dengan nama tabel di migration terakhir
    protected $table = 'ruangans';

    protected $primaryKey = 'kode_ruangan';
    public $incrementing = false;
    protected $keyType = 'string';

    // 🌟 'name' diubah menjadi 'ruangan_nama' sesuai rancangan kita
    protected $fillable = [
        'kode_ruangan',
        'lokasi',
        'ruangan_nama'
    ];

    /**
     * Relasi HasMany: Satu Ruangan memiliki Banyak Inventaris
     */
    public function inventaris()
    {
        // Parameter 2 disesuaikan menjadi 'inv_ruangan_kode' 
        // karena 'room_kode' sudah kita ganti di tabel inventaris
        return $this->hasMany(Inventaris::class, 'inv_ruangan_kode', 'kode_ruangan');
    }
}