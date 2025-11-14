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
            [
                'id' => 1,
                'sender_id' => 1,
                'type' => 'comment',
                'post_id' => null,
                'comment_id' => null,
                'created_at' => '2025-09-01 03:01:00'
            ],
            [
                'id' => 2,
                'sender_id' => 2,
                'type' => 'mention',
                'post_id' => null,
                'comment_id' => null,
                'created_at' => '2025-09-05 09:15:00'
            ],
            [
                'id' => 3,
                'sender_id' => 5,
                'type' => 'follow',
                'post_id' => null,
                'comment_id' => null,
                'created_at' => '2025-09-02 02:01:00'
            ],
        ]);
    }
}
