<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // 🌟 WAJIB DITAMBAHKAN untuk enkripsi password

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User Admin Utama
        User::create([
            'user_nama' => 'Admin SINDI',
            'user_email' => 'admin@gmail.com',
            'user_password' => Hash::make('password'),
            'user_status' => 'disetujui',
            'user_role_id' => 1, //=> 'Admin'
        ]);

        // User untuk Kecamatan Tawang
        User::create([
            'user_nama' => 'Kecamatan Tawang',
            'user_email' => 'tawang@gmail.com',
            'user_password' => Hash::make('password'),
            'user_status' => 'disetujui',
            'user_role_id' => 2, //=> 'Kecamatan'
        ]);

        // User untuk Kelurahan Lengkongsari
        User::create([
            'user_nama' => 'Kelurahan Lengkongsari',
            'user_email' => 'lengkongsari@gmail.com',
            'user_password' => Hash::make('password'),
            'user_status' => 'disetujui',
            'user_role_id' => 3, //=> 'User'
        ]);

        // User untuk Kelurahan Cikalang
        User::create([
            'user_nama' => 'Kelurahan Cikalang',
            'user_email' => 'cikalang@gmail.com',
            'user_password' => Hash::make('password'),
            'user_status' => 'disetujui',
            'user_role_id' => 3, //=> 'User'
        ]);

        // User untuk Kelurahan Empang
        User::create([
            'user_nama' => 'Kelurahan Empang',
            'user_email' => 'empang@gmail.com',
            'user_password' => Hash::make('password'),
            'user_status' => 'disetujui',
            'user_role_id' => 3, //=> 'User'
        ]);

        // User untuk Kelurahan Kahuripan
        User::create([
            'user_nama' => 'Kelurahan Kahuripan',
            'user_email' => 'kahuripan@gmail.com',
            'user_password' => Hash::make('password'),
            'user_status' => 'disetujui',
            'user_role_id' => 3, //=> 'User'
        ]);

        // User untuk Kelurahan Tawangsari
        User::create([
            'user_nama' => 'Kelurahan Tawangsari',
            'user_email' => 'tawangsari@gmail.com',
            'user_password' => Hash::make('password'),
            'user_status' => 'disetujui',
            'user_role_id' => 3, //=> 'User'
        ]);
    }
}