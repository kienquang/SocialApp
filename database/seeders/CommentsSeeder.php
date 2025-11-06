<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
=======
use App\Models\User;
use App\Models\Post;
>>>>>>> origin/kienBranch

class CommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
<<<<<<< HEAD
        DB::table('comments')->insert([
            ['id' => 1, 'user_id' => 3, 'post_id' => 1, 'parent_id' => null, 'content' => 'Bài viết rất hay, đọc mà nhớ quê hương.', 'created_at' => '2025-09-01 03:00:00'],
            ['id' => 2, 'user_id' => 5, 'post_id' => 1, 'parent_id' => 1, 'content' => 'Chuẩn luôn, mùa nước nổi đi ghe ngắm cảnh rất đẹp.', 'created_at' => '2025-09-01 03:20:00'],
            ['id' => 3, 'user_id' => 6, 'post_id' => 2, 'parent_id' => null, 'content' => 'Nhìn hoa anh đào mà muốn đi Nhật ngay lập tức.', 'created_at' => '2025-09-05 09:00:00'],
            ['id' => 4, 'user_id' => 8, 'post_id' => 2, 'parent_id' => 3, 'content' => 'Mình cũng vậy, mong một lần được trải nghiệm.', 'created_at' => '2025-09-05 09:10:00'],
        ]);
=======
        $userIds = User::pluck('id');
        // Chỉ comment (bình luận) vào các bài viết (posts) 'published'
        $postIds = Post::where('status', 'published')->pluck('id');
        $comments = [];

        $sampleComments = [
            'Bài viết rất hay, cảm ơn tác giả!',
            'Mình không đồng ý với quan điểm này lắm.',
            'Tuyệt vời, thông tin rất hữu ích.',
            'Cần thêm nhiều bài viết như thế này.',
            'Bạn có thể giải thích rõ hơn ở phần 2 không?'
        ];

        // 1. Tạo 15 bình luận gốc (top-level)
        for ($i = 0; $i < 15; $i++) {
            $comments[] = [
                'user_id' => $userIds->random(),
                'post_id' => $postIds->random(),
                'parent_id' => null, // Gốc
                'content' => $sampleComments[$i % count($sampleComments)],
                'status' => 'published',
                'created_at' => now()->subDays(rand(1, 10)),
                'updated_at' => now()->subDays(rand(1, 10)),
            ];
        }

        // Chèn (insert) và lấy ID của các bình luận gốc
        DB::table('comments')->insert($comments);
        $parentCommentIds = DB::table('comments')->whereNull('parent_id')->pluck('id');

        // 2. Tạo 10 bình luận phản hồi (replies)
        $replies = [];
        $sampleReplies = [
            'Đồng ý!',
            'Mình nghĩ khác...',
            'Chuẩn luôn.'
        ];

        for ($i = 0; $i < 10; $i++) {
            $parent = DB::table('comments')->find($parentCommentIds->random());

            $replies[] = [
                'user_id' => $userIds->random(),
                'post_id' => $parent->post_id, // Phải cùng post (bài viết) với cha
                'parent_id' => $parent->id, // Trả lời cha
                'content' => $sampleReplies[$i % count($sampleReplies)],
                'status' => 'published',
                'created_at' => now()->subDays(rand(1, 5)),
                'updated_at' => now()->subDays(rand(1, 5)),
            ];
        }

        DB::table('comments')->insert($replies);
>>>>>>> origin/kienBranch
    }
}
