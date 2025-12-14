<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peralatan extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'peralatans';

    /**
     * Kolom yang boleh diisi (Mass Assignable).
     */
    protected $fillable = [
        'lokasi',
        'kode_barang',
        'nama_barang',
        'nibr',                 // Nomor Induk Barang
        'nomor_register',
        'spesifikasi_barang',
        'merk_tipe',
        'Lok',                  // Pastikan nama kolom di database benar 'Lok' (kapital L)
        'spesifikasi_lainnya',
        'nomor_polisi',
        'nomor_rangka',
        'nomor_bpkb',
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
     * Konversi tipe data otomatis.
     */
    protected $casts = [
        'tanggal_perolehan' => 'date',
        'harga_satuan'      => 'decimal:2',
        'nilai_perolehan'   => 'decimal:2',
        'jumlah'            => 'integer', // Tambahan: pastikan jumlah dianggap angka bulat
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI (RELATIONSHIPS)
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke tabel BMDS (Daftar Penggunaan).
     * Satu Peralatan bisa memiliki banyak riwayat penggunaan.
     */
    public function bmds()
    {
        // Parameter kedua 'peralatan_id' harus sesuai dengan nama kolom foreign key di tabel bmds
        return $this->hasMany(Bmd::class, 'peralatan_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR (OPSIONAL)
    |--------------------------------------------------------------------------
    | Ini fitur tambahan agar Ivan mudah menampilkan format Rupiah di View.
    | Cara pakainya: $item->harga_formatted
    */

    public function getHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    public function getNilaiFormattedAttribute()
    {
        return 'Rp ' . number_format($this->nilai_perolehan, 0, ',', '.');
    }
}