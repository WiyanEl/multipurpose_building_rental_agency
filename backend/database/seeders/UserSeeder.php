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
    }
}
