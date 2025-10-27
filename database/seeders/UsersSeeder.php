<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Thêm Hash facade

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mật khẩu này được hash từ '12345678' như trong file của bạn
        $password = Hash::make('12345678');

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Luis1994',
                'email' => 'luis1994@example.com',
                'password' => $password,
                'role' => 'superadmin', // <-- SỬA LẠI: Khớp với CheckRole.php
                'google_id' => null,
                'facebook_id' => null,
                'avatar' => 'https://source.unsplash.com/_7LbC5J-jw4/600x600',
                'created_at' => '2025-08-13 20:17:50',
                'updated_at' => '2025-08-13 20:20:05',
            ],
            [
                'id' => 2,
                'name' => 'long nguyen',
                'email' => 'ninjalong161@gmail.com',
                'password' => $password,
                'role' => 'superadmin', // <-- SỬA LẠI: Khớp với CheckRole.php
                'google_id' => null,
                'facebook_id' => null,
                'avatar' => 'https://scr.vn/wp-content/uploads/2020/07/H%C3%ACnh-n%E1%BB%81n-hoa-anh-%C4%91%C3%A0o-th%E1%BA%ADt-tinh-kh%C3%B4i.jpg',
                'created_at' => '2025-08-13 20:51:34',
                'updated_at' => '2025-08-29 00:15:59',
            ],
            [
                'id' => 3,
                'name' => 'liem',
                'email' => '121@gmail.com',
                'password' => $password,
                'role' => 'user', // <-- THÊM VÀO: Chỉ định rõ vai trò
                'google_id' => null,
                'facebook_id' => null,
                'avatar' => null,
                'created_at' => '2025-08-13 21:09:33',
                'updated_at' => '2025-08-19 21:16:10',
            ],
            [
                'id' => 5,
                'name' => 'Long Nguyen Hoang',
                'email' => 'longnguyenid3@gmail.com',
                'password' => $password,
                'role' => 'user', // <-- THÊM VÀO: Chỉ định rõ vai trò
                'google_id' => null,
                'facebook_id' => null,
                'avatar' => 'https://khoinguonsangtao.vn/wp-content/uploads/2022/08/anh-que-huong-mien-tay-yen-binh.jpg',
                'created_at' => '2025-08-13 21:14:39',
                'updated_at' => '2025-08-29 00:16:24',
            ],
            [
                'id' => 6,
                'name' => 'Long Nguyen_1433957061140106',
                'email' => 'bangnguyen.02081979@gmail.com',
                'password' => $password,
                'role' => 'user', // <-- THÊM VÀO: Chỉ định rõ vai trò
                'google_id' => null,
                'facebook_id' => null,
                'avatar' => 'https://tse1.mm.bing.net/th/id/OIP.RXphT3K7pvLVpcSjZha3JQHaE8?rs=1&pid=ImgDetMain&o=7&rm=3',
                'created_at' => '2025-08-13 21:23:02',
                'updated_at' => '2025-08-29 00:48:40',
            ],
            [
                'id' => 8,
                'name' => 'long',
                'email' => 'abc@gmail.com',
                'password' => $password,
                'role' => 'user', // <-- THÊM VÀO: Chỉ định rõ vai trò
                'google_id' => null,
                'facebook_id' => null,
                'avatar' => 'https://tiki.vn/blog/wp-content/uploads/2023/01/oLkoHpw9cqRtLPTbg67bgtUvUdV1BnXRnAqqBZOVkEtPgf-_Ct3ADFJYXIjfDd0fTyECLEsWq5yZ2CCOEGxIsuHSmNNNUZQcnQT5-Ld6yoK19Q_Sphb0MmX64ga-O_TIPjItNkTL5ns4zqP1Z0OBzsIoeYKtcewnrjnVsw8vfG8uYwwCDkXaoozCrmH1kA.jpg',
                'created_at' => '2025-08-20 20:40:08',
                'updated_at' => '2025-08-29 00:17:13',
            ],
        ]);
    }
}
