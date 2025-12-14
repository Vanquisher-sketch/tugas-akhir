<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User Admin Utama
        User::create([
            'name' => 'Admin SINDI',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'status' => 'approved',
            'role_id' => '1', //=> 'Admin'
        ]);

        // User untuk Kecamatan Tawang
        User::create([
            'name' => 'Kecamatan Tawang',
            'email' => 'tawang@gmail.com',
            'password' => 'password',
            'status' => 'approved',
            'role_id' => '2', //=> 'User'
        ]);

        // User untuk Kelurahan Lengkongsari
        User::create([
            'name' => 'Kelurahan Lengkongsari',
            'email' => 'lengkongsari@gmail.com',
            'password' => 'password',
            'status' => 'approved',
            'role_id' => '3', //=> 'User'
        ]);

        // User untuk Kelurahan Cikalang
        User::create([
            'name' => 'Kelurahan Cikalang',
            'email' => 'cikalang@gmail.com',
            'password' => 'password',
            'status' => 'approved',
            'role_id' => '3', //=> 'User'
        ]);

        // User untuk Kelurahan Empang
        User::create([
            'name' => 'Kelurahan Empang',
            'email' => 'empang@gmail.com',
            'password' => 'password',
            'status' => 'approved',
            'role_id' => '3', //=> 'User'
        ]);

        // User untuk Kelurahan Kahuripan
        User::create([
            'name' => 'Kelurahan Kahuripan',
            'email' => 'kahuripan@gmail.com',
            'password' => 'password',
            'status' => 'approved',
            'role_id' => '3', //=> 'User'
        ]);

        // User untuk Kelurahan Tawangsari
        User::create([
            'name' => 'Kelurahan Tawangsari',
            'email' => 'tawangsari@gmail.com',
            'password' => 'password',
            'status' => 'approved',
            'role_id' => '3', //=> 'User'
        ]);
    }
}
