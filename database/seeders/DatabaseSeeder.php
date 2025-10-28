<?php

namespace Database\Seeders;

<<<<<<< HEAD
=======
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
>>>>>>> 9cd027d0762677104ef93ecbcb0df18f335517fe
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
<<<<<<< HEAD
=======
    use WithoutModelEvents;

>>>>>>> 9cd027d0762677104ef93ecbcb0df18f335517fe
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
<<<<<<< HEAD
        // Gọi các seeder theo đúng thứ tự phụ thuộc
        $this->call([
            UsersSeeder::class,
            PostsSeeder::class,
            FollowsSeeder::class,
            MessagesSeeder::class,
            NotificationsSeeder::class,
            CommentsSeeder::class,
            MessageReadsSeeder::class,
            MentionsSeeder::class,
            // Các bảng report không có dữ liệu mẫu nên không cần gọi seeder
=======
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
>>>>>>> 9cd027d0762677104ef93ecbcb0df18f335517fe
        ]);
    }
}
