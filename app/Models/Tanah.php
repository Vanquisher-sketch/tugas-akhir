<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tanah extends Model
{
    use HasFactory;

    protected $table = 'tanahs';

    protected $fillable = [
        'lokasi',
        'kode_barang',
        'nama_barang',
        'nibar',
        'nomor_register',
        'spesifikasi_barang', // Luas
        'spesifikasi_lainnya',
        'jumlah',
        'satuan',
        'Lok', // Ingat, L besar
        'titik_koordinat',
        'bukti_nama',
        'bukti_nomor',
        'bukti_tanggal',
        'nama_kepemilikan_dokumen',
        'nilai_perolehan',
        'harga_satuan',
        'cara_perolehan',
        'tanggal_perolehan',
        'status_penggunaan',
        'keterangan',
    ];

    protected $casts = [
        'bukti_tanggal' => 'date',
        'tanggal_perolehan' => 'date',
        // Casting decimal tidak wajib jika input sudah bersih, tapi bagus untuk output JSON
        'nilai_perolehan' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'jumlah' => 'decimal:2',
    ];
}