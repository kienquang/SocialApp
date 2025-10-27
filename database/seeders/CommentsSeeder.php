<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('comments')->insert([
            ['id' => 1, 'user_id' => 3, 'post_id' => 1, 'parent_id' => null, 'content' => 'Bài viết rất hay, đọc mà nhớ quê hương.', 'created_at' => '2025-09-01 03:00:00'],
            ['id' => 2, 'user_id' => 5, 'post_id' => 1, 'parent_id' => 1, 'content' => 'Chuẩn luôn, mùa nước nổi đi ghe ngắm cảnh rất đẹp.', 'created_at' => '2025-09-01 03:20:00'],
            ['id' => 3, 'user_id' => 6, 'post_id' => 2, 'parent_id' => null, 'content' => 'Nhìn hoa anh đào mà muốn đi Nhật ngay lập tức.', 'created_at' => '2025-09-05 09:00:00'],
            ['id' => 4, 'user_id' => 8, 'post_id' => 2, 'parent_id' => 3, 'content' => 'Mình cũng vậy, mong một lần được trải nghiệm.', 'created_at' => '2025-09-05 09:10:00'],
        ]);
    }
}
