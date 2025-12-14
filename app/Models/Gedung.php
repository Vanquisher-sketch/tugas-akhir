<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gedungs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lokasi',
        'kode_barang',
        'nama_barang',
        'nbar',
        'nomor_register',
        'spesifikasi_barang',
        'spesifikasi_lainnya',
        'jumlah_lantai',
        'Lok', // Nama kolom kedua untuk lokasi (dengan 'L' kapital)
        'titik_koordinat',
        'status_kepemilikan_tanah',
        'jumlah',
        'satuan',
        'harga_satuan',
        'nilai_perolehan',
        'cara_perolehan',
        'tanggal_perolehan',
        'status_penggunaan',
        'keterangan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_perolehan' => 'date',
        'harga_satuan' => 'decimal:2',
        'nilai_perolehan' => 'decimal:2',
    ];
}