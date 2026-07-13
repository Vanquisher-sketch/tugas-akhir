<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    // 1. Definisikan nama tabel
    protected $table = 'pegawais';

    // 2. Beritahu Laravel Primary Key kustom kita (menggunakan NIP)
    protected $primaryKey = 'pegawai_nip';

    // 3. Matikan auto-increment karena PK berupa String
    public $incrementing = false;

    // 4. Tentukan tipe data Primary Key adalah String
    protected $keyType = 'string';

    // 5. Update fillable dengan prefix 'pegawai_' (kecuali lokasi untuk filter)
    protected $fillable = [
        'pegawai_nip',
        'pegawai_nama',
        'pegawai_jabatan',
        'pegawai_no_hp',
        'pegawai_alamat',
        'pegawai_email',
        'lokasi' // Tetap murni tanpa awalan untuk kebutuhan filter sistem
    ];

    /**
     * 6. RELASI HAS MANY KE TABEL BMDS
     * Satu pegawai bisa memiliki banyak riwayat penyerahan/pemakaian BMD
     * Parameter 2: Foreign Key di tabel bmds (bmd_pegawai_nip)
     * Parameter 3: Local Key di tabel pegawais (pegawai_nip)
     */
    public function bmds()
    {
        return $this->hasMany(Bmd::class, 'bmd_pegawai_nip', 'pegawai_nip');
    }
}