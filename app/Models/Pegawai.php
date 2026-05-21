<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawais';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'no_hp',
        'email',
        'lokasi' // 🌟 Pastikan ini ada agar store() di PegawaiController & BmdController lancar
    ];

    /**
     * OPSI TAMBAHAN (Hubungan Kebalikan):
     * Satu pegawai bisa memiliki banyak riwayat pemakaian BMD
     */
    public function bmds()
    {
        return $this->hasMany(Bmd::class, 'pegawai_id', 'id');
    }
}