<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Cần để tạo slug
use Carbon\Carbon; // Cần để tạo timestamp

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Du lịch - Khám phá',
                'slug' => 'du-lich-kham-pha',
                'description' => 'Các bài viết về du lịch, khám phá vùng đất mới.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Văn hóa - Xã hội',
                'slug' => 'van-hoa-xa-hoi',
                'description' => 'Góc nhìn về văn hóa và các vấn đề xã hội.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Công nghệ',
                'slug' => 'cong-nghe',
                'description' => 'Tin tức và cập nhật về thế giới công nghệ.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
