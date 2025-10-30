<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gọi các seeder theo đúng thứ tự phụ thuộc
        $this->call([
            UsersSeeder::class,
            CategoriesSeeder::class, 
            PostsSeeder::class,
            FollowsSeeder::class,
            MessagesSeeder::class,
            NotificationsSeeder::class,
            CommentsSeeder::class,
            MessageReadsSeeder::class,
            MentionsSeeder::class,
            // Các bảng report không có dữ liệu mẫu nên không cần gọi seeder
        ]);
    }
}
