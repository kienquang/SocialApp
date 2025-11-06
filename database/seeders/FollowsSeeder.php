<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
=======
use App\Models\User;
>>>>>>> origin/kienBranch

class FollowsSeeder extends Seeder
{
    /**
     * Run the database seeds.
<<<<<<< HEAD
     */
    public function run(): void
    {
        DB::table('follows')->insert([
            ['id' => 1, 'follower_id' => 1, 'followed_id' => 2, 'created_at' => '2025-09-01 00:00:00'],
            ['id' => 2, 'follower_id' => 2, 'followed_id' => 1, 'created_at' => '2025-09-01 00:05:00'],
            ['id' => 3, 'follower_id' => 3, 'followed_id' => 1, 'created_at' => '2025-09-02 02:00:00'],
            ['id' => 4, 'follower_id' => 5, 'followed_id' => 2, 'created_at' => '2025-09-06 03:00:00'],
        ]);
=======
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
>>>>>>> origin/kienBranch
    }
}
