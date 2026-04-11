<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bmd extends Model
{
    use HasFactory;

    protected $table = 'bmds';

    // BMD tetap menggunakan ID auto-increment sebagai Primary Key 
    // karena satu Barang (Kode) bisa punya banyak riwayat pemakai.
    protected $primaryKey = 'id';

    protected $fillable = [
        'peralatan_kode', 'lokasi', 'alamat_penggunaan', 'pemakai_nama',
        'pemakai_status', 'pemakai_jabatan', 'pemakai_identitas', 
        'pemakai_alamat', 'nomor_pemakai', 'nomor_bendahara', 
        'tanggal_pajak', 'tanggal_stnk', 'bast_nomor', 'bast_tanggal', 
        'bast_file', 'dokumen_lain_nama', 'dokumen_lain_nomor', 
        'dokumen_lain_tanggal', 'keterangan'
    ];

    /**
     * Relasi ke Peralatan (KIB B)
     * Menghubungkan peralatan_kode di tabel bmds ke kode_barang di tabel peralatans
     */
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'peralatan_kode', 'kode_barang');
    }
}