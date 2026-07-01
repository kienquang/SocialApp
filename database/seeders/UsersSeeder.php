<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('12345678');
        $now = Carbon::now();

        $users = [
            // --- Quản trị viên ---
            [
                'id' => 1,
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => $password,
                'role' => 'superadmin',
                'avatar' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1762517867/laravel_posts/y5zqxzvkk4tttrj8edmj.jpg',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 2,
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => $password,
                'role' => 'admin',
                'avatar' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1762517867/laravel_posts/y5zqxzvkk4tttrj8edmj.jpg',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 3,
                'name' => 'Moderator',
                'email' => 'mod@example.com',
                'password' => $password,
                'role' => 'moderator',
                'avatar' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1762517867/laravel_posts/y5zqxzvkk4tttrj8edmj.jpg',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            // --- User (Người dùng) thường ---
            [
                'id' => 4,
                'name' => 'Alice',
                'email' => 'alice@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1762517867/laravel_posts/y5zqxzvkk4tttrj8edmj.jpg',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 5,
                'name' => 'Bob',
                'email' => 'bob@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1762517867/laravel_posts/y5zqxzvkk4tttrj8edmj.jpg',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 6,
                'name' => 'Charlie',
                'email' => 'charlie@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1762517867/laravel_posts/y5zqxzvkk4tttrj8edmj.jpg',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 7,
                'name' => 'David',
                'email' => 'david@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1762517867/laravel_posts/y5zqxzvkk4tttrj8edmj.jpg',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 8,
                'name' => 'Eve',
                'email' => 'eve@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1762517867/laravel_posts/y5zqxzvkk4tttrj8edmj.jpg',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'id' => 9,
                'name' => 'Frank',
                'email' => 'frank@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1762517867/laravel_posts/y5zqxzvkk4tttrj8edmj.jpg',
                'banned_until' => null,
                'created_at' => $now, 'updated_at' => $now
            ],
            // --- User (Người dùng) bị Ban (khóa) ---
            [
                'id' => 10,
                'name' => 'Banned User',
                'email' => 'banned@example.com',
                'password' => $password,
                'role' => 'user',
                'avatar' => 'https://res.cloudinary.com/dijvgjj4m/image/upload/v1762517867/laravel_posts/y5zqxzvkk4tttrj8edmj.jpg',
                'banned_until' => Carbon::now()->addDays(7), // Bị ban (khóa) 7 ngày
                'created_at' => $now, 'updated_at' => $now
            ],
        ];

        DB::table('users')->insert($users);
    }
}
