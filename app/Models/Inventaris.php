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

    protected $primaryKey = 'inv_kode_barang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'inv_kode_barang',
        'lokasi',
        'inv_ruangan_kode',
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
     * Generate Composite Key unifikasi untuk view/route
     */
    public function getKey()
    {
        return implode('|', [
            $this->getAttribute('inv_kode_barang'),
            $this->getAttribute('inv_ruangan_kode'),
            $this->getAttribute('inv_kondisi')
        ]);
    }

    /**
     * Accessor agar $item->id membaca nilai composite key di Blade View
     */
    public function getIdAttribute()
    {
        return $this->getKey();
    }

    /**
     * Helper static untuk mencari record (termasuk trashed) berdasarkan Composite Key string
     */
    public static function findByCompositeKey(string $compositeKey, bool $withTrashed = true)
    {
        $parts = explode('|', $compositeKey);

        if (count($parts) !== 3) {
            return null;
        }

        [$kodeBarang, $kodeRuangan, $kondisi] = $parts;

        $query = static::where('inv_kode_barang', $kodeBarang)
            ->where('inv_ruangan_kode', $kodeRuangan)
            ->where('inv_kondisi', $kondisi);

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->first();
    }

    /**
     * Override query penyimpan/penghapus agar tepat sasaran pada 3 kombinasi kolom
     */
    protected function setKeysForSaveQuery($query)
    {
        return $query->where('inv_kode_barang', $this->getAttribute('inv_kode_barang'))
                     ->where('inv_ruangan_kode', $this->getAttribute('inv_ruangan_kode'))
                     ->where('inv_kondisi', $this->getAttribute('inv_kondisi'));
    }

    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class, 'inv_kode_barang', 'alat_kode_barang');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'inv_ruangan_kode', 'kode_ruangan');
    }
}