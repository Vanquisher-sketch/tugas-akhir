<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // 1. Beritahu Laravel Primary Key yang baru
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_nama',
        'user_email',
        'user_password',
        'user_status',
        'user_role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'user_password',
        'user_remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_email_verified_at' => 'datetime',
            'user_password' => 'hashed',
        ];
    }

    // --- OVERRIDE METHOD AUTHENTICATION LARAVEL ---
    // Fungsi-fungsi di bawah ini WAJIB ada agar sistem Login (Auth) bawaan Laravel 
    // tahu kalau kita sudah mengganti nama kolom default-nya.

    public function getAuthPassword()
    {
        return $this->user_password;
    }

    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    public function getRememberTokenName()
    {
        return 'user_remember_token';
    }

    /**
     * 🌟 RELASI KE TABEL ROLES (Opsional tapi sangat disarankan)
     * Untuk mempermudah cek "User ini sebagai Admin atau Kecamatan?"
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'user_role_id', 'role_id');
    }
}