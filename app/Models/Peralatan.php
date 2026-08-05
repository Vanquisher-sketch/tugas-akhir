<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peralatan extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Definisikan nama tabel
    protected $table = 'peralatans';

    // 2. Beritahu Laravel Primary Key kustom kita
    protected $primaryKey = 'alat_kode_barang';

    // 3. Matikan auto-increment karena PK berupa String
    public $incrementing = false;
    protected $keyType = 'string';

    // 4. Daftarkan semua atribut yang diizinkan untuk diisi massal (Mass Assignment)
    protected $fillable = [
        'alat_kode_barang', 
        'lokasi', 
        'alat_nama_barang',  
        'alat_nomor_register',
        'alat_spesifikasi_barang', 
        'alat_merk_tipe', 
        'alat_lokasi_fisik', 
        'alat_spesifikasi_lainnya',
        'alat_nomor_polisi', 
        'alat_tanggal_pajak', 
        'alat_tanggal_stnk', 
        'alat_nomor_rangka', 
        'alat_nomor_bpkb', 
        'alat_jumlah', 
        'alat_satuan', 
        'alat_harga_satuan', 
        'alat_nilai_perolehan', 
        'alat_cara_perolehan', 
        'alat_tanggal_perolehan', 
        'alat_status_penggunaan', 
        'alat_kondisi', 
        'alat_keterangan',
        'alat_foto' // 🌟 ATRIBUT FOTO DITAMBAHKAN DI SINI
    ];

    /**
     * 🌟 RELASI HAS MANY KE TABEL BMDS
     * Satu peralatan bisa masuk ke banyak catatan serah terima (BAST/BMD)
     */
    public function bmds()
    {
        return $this->hasMany(Bmd::class, 'bmd_alat_kode', 'alat_kode_barang');
    }

    /**
     * 🌟 RELASI HAS MANY KE TABEL INVENTARIS
     * Satu peralatan master bisa didistribusikan ke banyak ruangan (tabel inventaris)
     */
    public function inventaris()
    {
        return $this->hasMany(Inventaris::class, 'inv_kode_barang', 'alat_kode_barang');
    }

    /**
     * 🌟 RELASI HAS MANY KE DETAIL PERALATAN
     * Satu peralatan master (misal: Laptop Acer) bisa memiliki banyak unit fisik (barcode berbeda)
     */
    public function detailPeralatans()
    {
        return $this->hasMany(DetailPeralatan::class, 'dt_alat_kode_barang', 'alat_kode_barang');
    }
}