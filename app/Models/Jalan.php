<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jalan extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Definisikan nama tabel secara eksplisit
    protected $table = 'jalans';

    // 2. Beritahu Laravel Primary Key yang baru
    protected $primaryKey = 'jalan_kode_barang';

    // 3. Matikan auto-increment karena PK berupa String
    public $incrementing = false;

    // 4. Tentukan tipe data Primary Key adalah String
    protected $keyType = 'string';

    
    // 6. Daftarkan semua kolom yang boleh diisi data sesuai dengan migration terbaru
    protected $fillable = [
        'jalan_kode_barang',
        'lokasi', // Kolom murni tanpa awalan khusus jangkar filter
        'jalan_nama_barang',
        'jalan_nomor_register',
        'jalan_spesifikasi_barang',
        'jalan_spesifikasi_lainnya',
        'jalan_nomor_ruas_jalan_jembatan_irigasi',
        'jalan_lokasi_fisik', // Perubahan dari 'Lok' agar selaras
        'jalan_titik_koordinat',
        'jalan_status_kepemilikan_tanah',
        'jalan_jumlah',
        'jalan_satuan',
        'jalan_harga_satuan',
        'jalan_nilai_perolehan',
        'jalan_cara_perolehan',
        'jalan_tanggal_perolehan',
        'jalan_status_penggunaan',
        'jalan_keterangan'
    ];
}