<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 🌟 Tambahkan SoftDeletes

class DetailPeralatan extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Sesuaikan nama tabel
    protected $table = 'detail_peralatans';

    // 2. Sesuaikan Primary Key kustom
    protected $primaryKey = 'dt_alat_id';

    // 3. Daftarkan kolom sesuai dengan migration terbaru
    protected $fillable = [
        'dt_alat_kode_barang',
        'dt_alat_kode_barcode',
        'dt_alat_kondisi',
        'lokasi', // Tetap murni tanpa awalan untuk filter
        'dt_alat_status_pinjam',
        'dt_alat_tanggal_cek',
        'dt_alat_foto',       // 🌟 KOLOM FOTO DITAMBAHKAN
        'dt_alat_keterangan'  // 🌟 KOLOM KETERANGAN DITAMBAHKAN
    ];

    /**
     * 4. 🌟 RELASI KE KIB B (Peralatan)
     * Mengubah relasi dari Inventaris menjadi Peralatan
     */
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'dt_alat_kode_barang', 'alat_kode_barang');
    }
}