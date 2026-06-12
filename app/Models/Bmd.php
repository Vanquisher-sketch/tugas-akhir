<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bmd extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'bmds';

    // 🌟 REVISI FILLABLE: Hanya mendaftarkan kolom yang ada di migration terbaru
    protected $fillable = [
        'peralatan_kode',
        'pegawai_id',
        'bendahara_id',
        'lokasi', // Tetap dipertahankan untuk filter wilayah URL (tawang, cikalang, dll)
        'pemakai_status',
        'pemakai_identitas',
        'bast_nomor',
        'bast_tanggal',
        'bast_file',
        'keterangan'
    ];

    /**
     * 🌟 RELASI KE KIB B (Peralatan)
     * BMD ini terhubung ke satu Peralatan berdasarkan 'peralatan_kode'
     */
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'peralatan_kode', 'kode_barang');
    }

    /**
     * 🌟 RELASI KE PEGAWAI (Sebagai Pemakai / Pihak Kedua)
     * BMD ini dipakai oleh satu Pegawai berdasarkan 'pegawai_id'
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id', 'id');
    }

    /**
     * 🌟 RELASI KE PEGAWAI (Sebagai Bendahara / Pihak Pertama)
     * BMD ini diserahkan oleh satu Bendahara berdasarkan 'bendahara_id'
     */
    public function bendahara()
    {
        return $this->belongsTo(Pegawai::class, 'bendahara_id', 'id');
    }
}