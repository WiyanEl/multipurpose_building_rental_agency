<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'statusenabled' => 1,
            'username' => 'admin_aplikasi',
            'email' => 'wiyan.be@student.unibi.ac.id',
            'fullname' => 'Programmer Administrator',
            'password' => Hash::make('@password@'),
            'user_role' => 1
        ]);

        DB::table('status_mitras')->insert(
            [
                'statusenabled' => 1,
                'namastatusmitra' => 'Belum Validasi'
            ],
            [
                'statusenabled' => 1,
                'namastatusmitra' => 'Sudah Validasi'
            ]
        );

        DB::table('role_user')->insert(
            [
                'statusenabled' => 1,
                'namarole' => 'Admin Aplikasi'
            ],
            [
                'statusenabled' => 1,
                'namarole' => 'Calon Mitra'
            ],
            [
                'statusenabled' => 1,
                'namarole' => 'Mitra Pengelola Gedung'
            ],
            [
                'statusenabled' => 1,
                'namarole' => 'Penyewa'
            ],
        );

        DB::table('status_pesanans')->insert(
            [
                'statusenabled' => 1,
                'namastatus' => 'Menunggu Pembayaran'
            ],
            [
                'statusenabled' => 1,
                'namastatus' => 'Uang Muka'
            ],
            [
                'statusenabled' => 1,
                'namastatus' => 'Belum Lunas'
            ],
            [
                'statusenabled' => 1,
                'namastatus' => 'Lunas'
            ],
            [
                'statusenabled' => 1,
                'namastatus' => 'Dibatalkan'
            ],
            [
                'statusenabled' => 1,
                'namastatus' => 'Sewa Manual'
            ]
        );
    }
}
