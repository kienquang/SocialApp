<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('notifications')->insert([
            ['id' => 1, 'sender_id' => 1, 'type' => 'comment', 'data' => 'User 3 đã bình luận vào bài viết của bạn.', 'created_at' => '2025-09-01 03:01:00'],
            ['id' => 2, 'sender_id' => 2, 'type' => 'mention', 'data' => 'User 8 đã nhắc đến bạn trong một bình luận.', 'created_at' => '2025-09-05 09:15:00'],
            ['id' => 3, 'sender_id' => 5, 'type' => 'follow', 'data' => 'User 3 đã theo dõi bạn.', 'created_at' => '2025-09-02 02:01:00'],
        ]);
    }
}
