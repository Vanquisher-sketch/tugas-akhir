<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bmd extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'bmds';

    /**
     * Atribut yang boleh diisi (Mass Assignable).
     */
    protected $fillable = [
        // 1. Relasi & Lokasi
        'peralatan_id',        // Barang
        'lokasi',              // Filter Sistem (tawang, kahuripan)
        'alamat_penggunaan',   // Lokasi Fisik (Aula, Rujab)

        // 2. Data Pemakai (Input di Menu BMD)
        'pemakai_nama',
        'pemakai_status',
        'pemakai_jabatan',
        'pemakai_identitas',
        'pemakai_alamat',

        // 3. Kontak & Pajak (Input/Edit di Menu Pajak)
        'nomor_pemakai',       // No WA
        'nomor_bendahara',     // No WA
        'tanggal_pajak',
        'tanggal_stnk',       // <--- [BARU] Khusus Jatuh Tempo Pajak (Agar tidak menimpa BAST)

        // 4. Dokumen BAST (Input di Menu BMD)
        'bast_nomor',
        'bast_tanggal',        // <--- Khusus Tanggal BAST
        'bast_file',           // File Upload

        // 5. Dokumen Lain
        'dokumen_lain_nama',
        'dokumen_lain_nomor',
        'dokumen_lain_tanggal',
        'keterangan',
    ];

    /**
     * Casting tipe data agar otomatis jadi objek tanggal (Carbon).
     */
    protected $casts = [
        'bast_tanggal' => 'date',
        'tanggal_pajak' => 'date', // <--- Penting untuk notifikasi/warna merah
        'dokumen_lain_tanggal' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI (RELATIONSHIPS)
    |--------------------------------------------------------------------------
    */

    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'peralatan_id');
    }
}