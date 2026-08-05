<?php

namespace App\Models;

use App\Models\Ruangan;
use App\Models\Peralatan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventaris extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventaris';

    /**
     * Composite Primary Key menggunakan 3 kolom:
     * (inv_kode_barang + inv_ruangan_kode + inv_kondisi)
     * Agar barang yang sama di ruangan yang sama dapat dipisah berdasarkan kondisinya (Baik/Rusak).
     */
    protected $primaryKey = ['inv_kode_barang', 'inv_ruangan_kode', 'inv_kondisi'];
    public $incrementing = false;
    protected $keyType = 'string';

    // Daftarkan atribut dengan prefix 'inv_' dan 'lokasi'
    protected $fillable = [
        'inv_kode_barang',
        'lokasi',
        'inv_ruangan_kode',
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
     * Override getKey() agar Eloquent dapat mengenali identifier unik dari 3 kombinasi kolom.
     */
    public function getKey()
    {
        return $this->getAttribute('inv_kode_barang') . '-' . 
               $this->getAttribute('inv_ruangan_kode') . '-' . 
               $this->getAttribute('inv_kondisi');
    }

    /**
     * Override setKeysForSaveQuery() agar eksekusi UPDATE, DELETE, dan SoftDeletes
     * menargetkan baris data spesifik berdasarkan kode barang, ruangan, dan kondisinya.
     */
    protected function setKeysForSaveQuery($query)
    {
        return $query->where('inv_kode_barang', $this->getAttribute('inv_kode_barang'))
                     ->where('inv_ruangan_kode', $this->getAttribute('inv_ruangan_kode'))
                     ->where('inv_kondisi', $this->getAttribute('inv_kondisi'));
    }

    /**
     * 🌟 RELASI KE MASTER PERALATAN (KIB B)
     */
    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'inv_kode_barang', 'alat_kode_barang');
    }

    /**
     * 🌟 RELASI KE RUANGAN
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'inv_ruangan_kode', 'kode_ruangan');
    }
}