<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class ReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy user 5-10 làm người báo cáo (reporter)
        $reporters = User::where('id', '>', 4)->pluck('id');

        // Lấy user 1-4 làm người bị báo cáo
        $reportedUsers = User::where('id', '<=', 4)->pluck('id');
        $reportedPosts = Post::pluck('id')->take(5);
        $reportedComments = Comment::pluck('id')->take(5);

        // 1. Báo cáo Post (Bài viết)
        DB::table('report_posts')->insert([
            [
                'post_id' => $reportedPosts->random(),
                'reporter_id' => $reporters->random(),
                'reason' => 'Nội dung bài viết này là spam và quảng cáo.',
                'created_at' => now(),
            ],
            [
                'post_id' => $reportedPosts->random(),
                'reporter_id' => $reporters->random(),
                'reason' => 'Bài viết có chứa hình ảnh không phù hợp.',
                'created_at' => now(),
            ],
        ]);

        // 2. Báo cáo Comment (Bình luận)
        DB::table('report_comments')->insert([
            [
                'comment_id' => $reportedComments->random(),
                'reporter_id' => $reporters->random(),
                'reason' => 'Bình luận này sử dụng ngôn từ thù hận.',
                'created_at' => now(),
            ],
        ]);

        // 3. Báo cáo User (Người dùng)
        DB::table('report_users')->insert([
            [
                'reported_user_id' => $reportedUsers->random(),
                'reporter_id' => $reporters->random(),
                'reason' => 'Avatar (Ảnh đại diện) của user (người dùng) này không phù hợp.',
                'created_at' => now(),
            ],
            [
                'reported_user_id' => $reportedUsers->random(),
                'reporter_id' => $reporters->random(),
                'reason' => 'Tên của user (người dùng) này vi phạm quy tắc.',
                'created_at' => now(),
            ],
        ]);
    }
}
