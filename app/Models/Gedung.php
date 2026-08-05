<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gedung extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Definisikan nama tabel
    protected $table = 'gedungs';

    // 2. Beritahu Laravel Primary Key yang baru
    protected $primaryKey = 'gedung_kode_barang';

    // 3. Matikan auto-increment karena PK berupa String
    public $incrementing = false;

    // 4. Tentukan tipe data Primary Key adalah String
    protected $keyType = 'string';


    // 6. Daftarkan semua kolom yang boleh diisi data sesuai migration terbaru
    protected $fillable = [
        'gedung_kode_barang',
        'lokasi', // Kolom murni tanpa awalan khusus jangkar filter
        'gedung_nama_barang',
        'gedung_nomor_register',
        'gedung_spesifikasi_barang',
        'gedung_spesifikasi_lainnya',
        'gedung_jumlah_lantai',
        'gedung_lokasi_fisik', // Perubahan dari 'Lok'
        'gedung_titik_koordinat',
        'gedung_status_kepemilikan_tanah',
        'gedung_jumlah',
        'gedung_satuan',
        'gedung_harga_satuan',
        'gedung_nilai_perolehan',
        'gedung_cara_perolehan',
        'gedung_tanggal_perolehan',
        'gedung_status_penggunaan',
        'gedung_keterangan'
    ];
}