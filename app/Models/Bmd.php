<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bmd extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Definisikan nama tabel secara eksplisit
    protected $table = 'bmds';

    // 2. Tentukan Primary Key kustom (karena bukan 'id')
    protected $primaryKey = 'bmd_id';



    // 4. Properti fillable yang disesuaikan dengan atribut migration terbaru
    protected $fillable = [
        'bmd_alat_kode',
        'bmd_pegawai_nip',
        'bmd_bendahara_nip',
        'lokasi', // Kolom murni tanpa awalan khusus jangkar filter
        'bmd_pemakai_status',
        'bmd_pemakai_identitas',
        'bmd_bast_nomor',
        'bmd_bast_tanggal',
        'bmd_bast_file',
        'bmd_keterangan'
    ];

    /**
     * 5. RELASI KE KIB B (Peralatan)
     * Menghubungkan bmd_alat_kode ke primary key 'alat_kode_barang' di tabel peralatans
     */
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'bmd_alat_kode', 'alat_kode_barang');
    }

    /**
     * 6. RELASI KE PEGAWAI (Sebagai Pemakai / Pihak Kedua)
     * Menghubungkan bmd_pegawai_nip ke primary key 'pegawai_nip' di tabel pegawais
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'bmd_pegawai_nip', 'pegawai_nip');
    }

    /**
     * 7. RELASI KE PEGAWAI (Sebagai Bendahara / Pihak Pertama)
     * Menghubungkan bmd_bendahara_nip ke primary key 'pegawai_nip' di tabel pegawais
     */
    public function bendahara()
    {
        return $this->belongsTo(Pegawai::class, 'bmd_bendahara_nip', 'pegawai_nip');
    }
}