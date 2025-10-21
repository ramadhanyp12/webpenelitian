<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        Profile::create([
            'user_id' => $admin->id,
            'phone' => '081234567890',
            'kampus' => 'Universitas Contoh',
            'nim' => '000000001',
            'prodi' => 'Teknik Informatika',
            'konsentrasi' => 'Sistem Informasi',
        ]);

        // Buat user mahasiswa
        $mahasiswa = User::create([
            'name' => 'Mahasiswa User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        Profile::create([
            'user_id' => $mahasiswa->id,
            'phone' => '089876543210',
            'kampus' => 'Universitas Contoh',
            'nim' => '000000002',
            'prodi' => 'Hukum',
            'konsentrasi' => 'Perdata',
        ]);
    }
}
