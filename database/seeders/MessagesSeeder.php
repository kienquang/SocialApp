<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('messages')->insert([
            ['id' => 1, 'sender_id' => 1, 'receiver_id' => 2, 'content' => 'Chào bạn, bạn có đi miền Tây bao giờ chưa?', 'created_at' => '2025-09-01 01:00:00'],
            ['id' => 2, 'sender_id' => 2, 'receiver_id' => 1, 'content' => 'Mình đi rồi, cảnh rất đẹp bạn ạ.', 'created_at' => '2025-09-01 01:05:00'],
            ['id' => 3, 'sender_id' => 3, 'receiver_id' => 5, 'content' => 'Bạn có tham gia lễ hội hoa anh đào chưa?', 'created_at' => '2025-09-05 08:40:00'],
        ]);
    }
}
