<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MentionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mentions')->insert([
            ['id' => 1, 'comment_id' => 2, 'post_id' => 1, 'mentioned_user_id' => 1, 'mentioner_user_id' => 5, 'created_at' => '2025-09-01 03:21:00'],
            ['id' => 2, 'comment_id' => 4, 'post_id' => 2, 'mentioned_user_id' => 2, 'mentioner_user_id' => 8, 'created_at' => '2025-09-05 09:12:00'],
        ]);
    }
}
