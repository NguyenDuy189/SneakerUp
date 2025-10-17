<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Xoá user admin cũ nếu tồn tại (tránh lỗi unique email)
        DB::table('users')->where('email', 'admin@sneakerup.com')->delete();

        // Tạo tài khoản admin mặc định
        User::create([
            'fullname' => 'Admin SneakerUp',
            'username' => 'admin',
            'email' => 'admin@sneakerup.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '0900000000',
            'created_at' => now(),
        ]);
    }
}
