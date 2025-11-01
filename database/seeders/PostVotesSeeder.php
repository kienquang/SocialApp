<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Post;

class PostVotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::pluck('id');
        $posts = Post::pluck('id');
        $votes = [];

        // Tạo 20-30 lượt vote (bầu chọn) ngẫu nhiên
        for ($i = 0; $i < 30; $i++) {
            $userId = $users->random();
            $postId = $posts->random();

            // 80% là upvote (1), 20% là downvote (-1)
            $voteValue = (rand(1, 10) <= 8) ? 1 : -1;

            $uniqueKey = $userId . '-' . $postId;

            // Đảm bảo một user (người dùng) chỉ vote (bầu chọn) 1 post (bài viết) 1 lần
            if (!isset($votes[$uniqueKey])) {
                $votes[$uniqueKey] = [
                    'user_id' => $userId,
                    'post_id' => $postId,
                    'vote' => $voteValue,
                    'created_at' => now()->subDays(rand(1, 15)),
                    'updated_at' => now()->subDays(rand(1, 15)),
                ];
            }
        }

        DB::table('post_votes')->insert(array_values($votes));
    }
}
