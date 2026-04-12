<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    // Karena Primary Key Room juga pakai kode_ruangan (String)
    protected $primaryKey = 'kode_ruangan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['kode_ruangan', 'name', 'lokasi'];

    /**
     * Relasi HasMany: Satu Ruangan memiliki Banyak Inventaris
     */
    public function inventaris()
    {
        // Parameter 1: Model tujuan
        // Parameter 2: Foreign Key di tabel inventaris (room_kode)
        // Parameter 3: Local Key di tabel rooms (kode_ruangan)
        return $this->hasMany(Inventaris::class, 'room_kode', 'kode_ruangan');
    }
}