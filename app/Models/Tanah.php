<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tanah extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Definisikan nama tabel secara eksplisit
    protected $table = 'tanahs';

    // 2. Beritahu Laravel Primary Key yang baru
    protected $primaryKey = 'tanah_kode_barang';

    // 3. Matikan auto-increment karena PK berupa String
    public $incrementing = false;

    // 4. Tentukan tipe data Primary Key adalah String
    protected $keyType = 'string';

    // 6. Daftarkan semua kolom yang boleh diisi data sesuai dengan migration terbaru
    protected $fillable = [
        'tanah_kode_barang',
        'lokasi', // Kolom murni tanpa awalan khusus jangkar filter
        'tanah_nama_barang',
        'tanah_nomor_register',
        'tanah_spesifikasi_barang',
        'tanah_spesifikasi_lainnya',
        'tanah_jumlah',
        'tanah_satuan',
        'tanah_lokasi_fisik', // Perubahan dari 'Lok' agar selaras
        'tanah_titik_koordinat',
        'tanah_bukti_nama',
        'tanah_bukti_nomor',
        'tanah_bukti_tanggal',
        'tanah_nama_kepemilikan_dokumen',
        'tanah_nilai_perolehan',
        'tanah_harga_satuan',
        'tanah_cara_perolehan',
        'tanah_tanggal_perolehan',
        'tanah_status_penggunaan',
        'tanah_keterangan'
    ];
}