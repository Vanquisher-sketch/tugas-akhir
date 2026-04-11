<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    /**
     * Set Primary Key ke kode_ruangan
     */
    protected $primaryKey = 'kode_ruangan';

    /**
     * Matikan Auto-Increment karena Primary Key kita bukan Integer
     */
    public $incrementing = false;

    /**
     * Set tipe data Primary Key menjadi String
     */
    protected $keyType = 'string';

    protected $fillable = [
        'kode_ruangan', // Sekarang ini wajib masuk fillable karena diisi manual/system
        'lokasi',
        'name',
    ];
}