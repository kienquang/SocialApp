<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Du lịch - Khám phá', 'description' => 'Chia sẻ hành trình và trải nghiệm du lịch.'],
            ['name' => 'Văn hóa - Xã hội', 'description' => 'Góc nhìn về các vấn đề văn hóa và xã hội.'],
            ['name' => 'Công nghệ', 'description' => 'Tin tức, thủ thuật và đánh giá sản phẩm công nghệ.'],
            ['name' => 'Khoa học', 'description' => 'Khám phá tri thức và các phát kiến khoa học.'],
            ['name' => 'Giải trí', 'description' => 'Bình luận phim, âm nhạc và các sự kiện giải trí.'],
        ];

        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('categories')->insert($data);
    }
}
