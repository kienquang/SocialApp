<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class FollowsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của tất cả User (người dùng) (từ 1 đến 10)
        $users = User::pluck('id');
        $follows = [];

        // Tạo 15-20 lượt theo dõi ngẫu nhiên
        for ($i = 0; $i < 20; $i++) {
            $followerId = $users->random();
            $followedId = $users->random();

            // Đảm bảo user (người dùng) không tự theo dõi chính mình
            if ($followerId === $followedId) {
                continue;
            }

            // Tạo một "key" (khóa) duy nhất để tránh trùng lặp
            $uniqueKey = $followerId . '-' . $followedId;

            if (!isset($follows[$uniqueKey])) {
                 $follows[$uniqueKey] = [
                    'follower_id' => $followerId,
                    'followed_id' => $followedId,
                    'created_at' => now()->subDays(rand(1, 30)), // Giả lập thời gian
                ];
            }
        }

        // Xóa key (khóa) và chèn (insert) vào CSDL (database)
        DB::table('follows')->insert(array_values($follows));
    }
}
