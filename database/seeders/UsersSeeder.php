<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
use Illuminate\Support\Facades\Hash; // Thêm Hash facade
=======
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
>>>>>>> origin/kienBranch

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
<<<<<<< HEAD
        // Hash mật khẩu '123456' một lần
        $password = Hash::make('12345678');

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Luis1994',
                'email' => 'luis1994@example.com',
                'password' => $password, // Cập nhật password
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
                'password' => $password, // Cập nhật password
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
                'password' => $password, // Cập nhật password
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
                'password' => $password, // Cập nhật password
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
                'password' => $password, // Cập nhật password
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
                'password' => $password, // Cập nhật password
                'google_id' => null,
                'facebook_id' => null,
                'avatar' => 'https://tiki.vn/blog/wp-content/uploads/2023/01/oLkoHpw9cqRtLPTbg67bgtUvUdV1BnXRnAqqBZOVkEtPgf-_Ct3ADFJYXIjfDd0fTyECLEsWq5yZ2CCOEGxIsuHSmNNNUZQcnQT5-Ld6yoK19Q_Sphb0MmX64ga-O_TIPjItNkTL5ns4zqP1Z0OBzsIoeYKtcewnrjnVsw8vfG8uYwwCDkXaoozCrmH1kA.jpg',
                'created_at' => '2025-08-20 20:40:08',
                'updated_at' => '2025-08-29 00:17:13',
            ],
        ]);
=======
        $password = Hash::make('12345678');
        $now = Carbon::now();

        $users = [
            // --- Quản trị viên ---
            [
                'id' => 1,
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => $password,
                'role' => 'superadmin',
                'avatar' => 'https://i.pravatar.cc/150?img=1',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 2,
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => $password,
                'role' => 'admin',
                'avatar' => 'https://i.pravatar.cc/150?img=2',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 3,
                'name' => 'Moderator',
                'email' => 'mod@example.com',
                'password' => $password,
                'role' => 'moderator',
                'avatar' => 'https://i.pravatar.cc/150?img=3',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            // --- User (Người dùng) thường ---
            [
                'id' => 4,
                'name' => 'Alice',
                'email' => 'alice@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://i.pravatar.cc/150?img=4',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 5,
                'name' => 'Bob',
                'email' => 'bob@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://i.pravatar.cc/150?img=5',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 6,
                'name' => 'Charlie',
                'email' => 'charlie@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://i.pravatar.cc/150?img=6',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 7,
                'name' => 'David',
                'email' => 'david@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://i.pravatar.cc/150?img=7',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 8,
                'name' => 'Eve',
                'email' => 'eve@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://i.pravatar.cc/150?img=8',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 9,
                'name' => 'Frank',
                'email' => 'frank@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://i.pravatar.cc/150?img=9',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            // --- User (Người dùng) bị Ban (khóa) ---
            [
                'id' => 10,
                'name' => 'Banned User',
                'email' => 'banned@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://i.pravatar.cc/150?img=10',
                'banned_until' => Carbon::now()->addDays(7), // Bị ban (khóa) 7 ngày
                'created_at' => $now, 'updated_at' => $now
            ],
        ];

        DB::table('users')->insert($users);
>>>>>>> origin/kienBranch
    }
}
