<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'role_id' => 1,
            'role_nama' => 'Admin',
        ]);

        Role::create([
            'role_id' => 2,
            'role_nama' => 'Kecamatan',
        ]);

        Role::create([
            'role_id' => 3,
            'role_nama' => 'User',
        ]);
    }
}