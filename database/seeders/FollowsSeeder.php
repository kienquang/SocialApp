<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('follows')->insert([
            ['id' => 1, 'follower_id' => 1, 'followed_id' => 2, 'created_at' => '2025-09-01 00:00:00'],
            ['id' => 2, 'follower_id' => 2, 'followed_id' => 1, 'created_at' => '2025-09-01 00:05:00'],
            ['id' => 3, 'follower_id' => 3, 'followed_id' => 1, 'created_at' => '2025-09-02 02:00:00'],
            ['id' => 4, 'follower_id' => 5, 'followed_id' => 2, 'created_at' => '2025-09-06 03:00:00'],
        ]);
    }
}
