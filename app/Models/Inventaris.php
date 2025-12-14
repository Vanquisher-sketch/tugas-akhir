<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventaris extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventaris';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lokasi',
        'room_id',
        'nibar',
        'nomor_register',
        'kode_barang',
        'nama_barang',
        'spesifikasi_barang',
        'merk_tipe',
        'tahun_perolehan',
        'jumlah',
        'satuan',
        'keterangan',
    ];
    
    /**
     * Mendefinisikan relasi ke model Room.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}