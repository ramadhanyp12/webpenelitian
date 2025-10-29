<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // 1) Admin utama (sudah ada)
        // =========================
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'phone'       => '081234567890',
                'kampus'      => 'Universitas Contoh',
                'nim'         => '000000001',
                'prodi'       => 'Teknik Informatika',
                'konsentrasi' => 'Sistem Informasi',
            ]
        );

        // =========================
        // 2) Tambah 2 admin baru
        // =========================
        $adminsBaru = [
            [
                'name'  => 'Admin Utama',
                'email' => 'pratamaramadhan08@gmail.com',
                'phone' => '082145667305',
                'nim'   => '000000010', // pastikan unik
            ],
            [
                'name'  => 'Super Admin',
                'email' => 'surat@pta-gorontalo.go.id',
                'phone' => '08114312250',
                'nim'   => '000000011', // pastikan unik
            ],
        ];

        foreach ($adminsBaru as $a) {
            $u = User::updateOrCreate(
                ['email' => $a['email']],
                [
                    'name'     => $a['name'],
                    'password' => Hash::make('password'), // ganti setelah login
                    'role'     => 'admin',
                ]
            );

            Profile::updateOrCreate(
                ['user_id' => $u->id],
                [
                    'phone'       => $a['phone'],
                    'kampus'      => 'Universitas Contoh',
                    'nim'         => $a['nim'], // kalau kolom nim unique, wajib unik
                    'prodi'       => 'Administrasi',
                    'konsentrasi' => 'Umum',
                ]
            );
        }

        // =========================
        // 3) User mahasiswa contoh
        // =========================
        $mahasiswa = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'     => 'Mahasiswa User',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $mahasiswa->id],
            [
                'phone'       => '089876543210',
                'kampus'      => 'Universitas Contoh',
                'nim'         => '000000002',
                'prodi'       => 'Hukum',
                'konsentrasi' => 'Perdata',
            ]
        );
    }
}
