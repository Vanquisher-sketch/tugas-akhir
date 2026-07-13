<?php

namespace App\Models;

use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventaris extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventaris';

    // 1. Tentukan Primary Key kustom sesuai prefix
    protected $primaryKey = 'inv_kode_barang';
    
    // 2. Matikan auto-increment karena PK berupa string
    public $incrementing = false;
    protected $keyType = 'string';

    

    // 4. Daftarkan atribut dengan prefix 'inv_' (kecuali lokasi untuk filter)
    protected $fillable = [
        'inv_kode_barang',
        'lokasi',
        'inv_ruangan_kode', // Menggantikan 'room_kode' agar rapi
        'inv_nibar',
        'inv_nomor_register',
        'inv_nama_barang',
        'inv_spesifikasi_barang',
        'inv_merk_tipe',
        'inv_tahun_perolehan',
        'inv_jumlah',
        'inv_satuan',
        'inv_kondisi', 
        'inv_keterangan'
    ];

    /**
     * 🌟 RELASI KE MASTER PERALATAN (KIB B)
     * Menghubungkan inv_kode_barang di tabel inventaris 
     * ke alat_kode_barang di tabel peralatans.
     */
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'inv_kode_barang', 'alat_kode_barang');
    }

    /**
     * 🌟 RELASI KE RUANGAN
     * Menghubungkan inventaris ini dengan ruangan tempat barang tersebut berada
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'inv_ruangan_kode', 'kode_ruangan');
    }
}