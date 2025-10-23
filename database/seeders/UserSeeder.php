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
        // Xóa tài khoản admin cũ nếu tồn tại
        DB::table('users')->where('email', 'admin@sneakerup.com')->delete();

        // Tạo admin chính
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

        // ✅ Tạo 5 nhân viên (staff)
        $staffs = [
            [
                'fullname' => 'Nguyễn Văn An',
                'username' => 'staff1',
                'email' => 'staff1@sneakerup.com',
                'phone' => '0911111111',
            ],
            [
                'fullname' => 'Trần Thị Bình',
                'username' => 'staff2',
                'email' => 'staff2@sneakerup.com',
                'phone' => '0922222222',
            ],
            [
                'fullname' => 'Lê Văn Cường',
                'username' => 'staff3',
                'email' => 'staff3@sneakerup.com',
                'phone' => '0933333333',
            ],
            [
                'fullname' => 'Phạm Thị Dung',
                'username' => 'staff4',
                'email' => 'staff4@sneakerup.com',
                'phone' => '0944444444',
            ],
            [
                'fullname' => 'Hoàng Văn Em',
                'username' => 'staff5',
                'email' => 'staff5@sneakerup.com',
                'phone' => '0955555555',
            ],
        ];

        foreach ($staffs as $staff) {
            User::updateOrCreate(
                ['email' => $staff['email']],
                [
                    'fullname' => $staff['fullname'],
                    'username' => $staff['username'],
                    'password' => Hash::make('123456'),
                    'role' => 'staff',
                    'status' => 'active',
                    'phone' => $staff['phone'],
                    'created_at' => now(),
                ]
            );
        }

        // Tạo 10 user khách hàng
        User::factory(10)->create();
    }
}
